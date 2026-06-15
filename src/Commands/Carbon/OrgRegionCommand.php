<?php

namespace Pantheon\TerminusCarbon\Commands\Carbon;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Pantheon\Terminus\Commands\StructuredListTrait;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Pantheon\Terminus\Commands\Org\Site\ListCommand;
use Pantheon\TerminusCarbon\Model\Regions;

class OrgRegionCommand extends ListCommand
{
    use SiteAwareTrait;
    use StructuredListTrait;

    /**
     * Displays the list of sites associated with an organization with carbon data.
     *
     * @authorize
     * @filter-output
     *
     * @command carbon:org
     * @aliases org:carbon:list
     *
     * @field-labels
     *     name: Name
     *     id: ID
     *     plan_name: Plan
     *     framework: Framework
     *     owner: Owner
     *     created: Created
     *     upstream: Upstream
     *     tags: Tags
     *     region: Region
     *     frozen: Is Frozen?
     *     datacenter: Datacenter
     *     cfe: Carbon Free Energy % (CFE)
     *     grid_carbon_intensity: Grid Carbon Intensity (gCO2eq/kWh)
     *
     * @default-fields id,name,framework,region,datacenter,cfe,grid_carbon_intensity
     *
     * @return RowsOfFields
     *
     * @param string $organization Organization name, label, or ID
     * @option string $upstream Upstream name to filter
     *
     * @usage <organization> Displays the list of sites associated with <organization> with carbon data.
     * @usage <organization> --upstream=<upstream> Displays the list of sites associated with <organization> with the upstream having UUID <upstream>.
     */
    public function listSites($organization, $options = ['plan' => null, 'tag' => null, 'upstream' => null,])
    {
        $org = $this->session()->getUser()->getOrganizationMemberships()->get($organization)->getOrganization();
        $this->sites->fetch(['org_id' => $org->id,]);
        if (isset($options['plan']) && !is_null($plan = $options['plan'])) {
            $this->sites->filterByPlanName($plan);
        }
        if (!is_null($tag = $options['tag'])) {
            $this->sites->filterByTag($tag);
        }
        if (!is_null($upstream = $options['upstream'])) {
            $this->sites->filterByUpstream($upstream);
        }

        // Define ideal attributes and serialize site data to merge.
        $carbon_attributes = ['cfe', 'grid_carbon_intensity', 'datacenter'];
        $sites = $this->sites->serialize();
        $regions = new Regions();

        // Merge carbon data
        foreach ($sites as $id => $site) {
            $region_name = $site['region'];
            $region_data = array_values($regions->filterByRegionCode($region_name))[0];
            foreach ($carbon_attributes as $attr) {
                $sites[$id][$attr] = $region_data[$attr];
            }
        }

        return new RowsOfFields($sites);
    }
}
