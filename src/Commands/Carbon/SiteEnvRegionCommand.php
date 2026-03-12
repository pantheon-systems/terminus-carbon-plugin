<?php

namespace Pantheon\TerminusCarbon\Commands\Carbon;

use Consolidation\OutputFormatters\StructuredData\PropertyList;
use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Commands\StructuredListTrait;
use Pantheon\Terminus\Exceptions\TerminusException;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Pantheon\TerminusCarbon\Model\CarbonSiteEnv;
use Pantheon\TerminusCarbon\Model\Regions;

class SiteEnvRegionCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;
    use StructuredListTrait;

    /**
     * Displays environment status and configuration.
     *
     * @authorize
     *
     * @command carbon:env
     * @aliases env:carbon:info
     *
     * @param string $site_env Site & environment in the format `site-name.env`
     *
     * @field-labels
     *     id: ID
     *     name: Name
     *     label: Label
     *     created: Created
     *     framework: Framework
     *     region: Region
     *     organization: Organization
     *     plan_name: Plan
     *     upstream_id: Upstream ID
     *     upstream_machine_name: Upstream Name
     *     upstream_label: Upstream Label
     *     upstream_repository_url: Upstream URL
     *     owner: Owner
     *     datacenter: Datacenter
     *     cfe: Carbon Free Energy % (CFE)
     *     grid_carbon_intensity: Grid Carbon Intensity (gCO2eq/kWh)
     *     appserver: Application Container (qty)
     *     dbserver: Database Container (qty)
     *     indexserver: Search Container (qty)
     *     cacheserver: Object Cache Container (qty)
     *     cms_kind: CMS Kind
     *     cms_version: CMS Version
     *
     * @default-fields id,name,cms_kind,cms_version,upstream_machine_name,region,datacenter,cfe,grid_carbon_intensity,appserver,dbserver,indexserver,cacheserver
     *
     * @return PropertyList
     *
     * @usage <site>.<env> Displays the infrastructure overview for <site>'s <env> environment.
     *
     * @throws TerminusException
     */
    public function carbonEnvInfo(string $site_env): PropertyList
    {
        $this->requireSiteIsNotFrozen($site_env);

        $env = $this->getEnv($site_env);

	$site_name = explode(".",$site_env);	
	$site = $this->getSiteById($site_name[0]);

        $region = new Regions();
        $site_data = $region->mergeRegionData($site);

        // Extract binding data.
        $bindings = $env->getBindings();
        $carbonEnv = new CarbonSiteEnv();
        $container_types = ['appserver', 'dbserver', 'indexserver', 'cacheserver'];

        // Prefill container types for consistency.
        foreach ($container_types as $type) {
            $site_data[$type] = 0;
        }

        // Attach binding data.
        foreach ($bindings->all() as $binding) {
            $binding_type = $binding->get('type');
            $binding_env = $binding->get('environment');
            if (
                $binding_env == $env->get('id')
                && in_array($binding_type, $container_types)
            ) {
                $type_count = !empty($carbonEnv->get($binding_type)) ? $carbonEnv->get($binding_type) : 0;
                $carbonEnv->set($binding_type, $type_count + 1);
            }
        }

        // Extract runtime info, append score.
        $runtime = json_decode(json_encode($env->get('framework_runtime')), true);
        foreach ($runtime as $key => $value) {
            if (in_array($key, ['kind', 'version'])) {
                $runtime['cms_' . $key] = $value;
            }
            unset($runtime[$key]);
        }

        $site_data = array_merge($site_data, $carbonEnv->serialize(), $runtime);

        return new PropertyList($site_data);
    }
}
