<?php

namespace Pantheon\TerminusCarbon\Commands\Carbon;

use Consolidation\OutputFormatters\StructuredData\PropertyList;
use Pantheon\Terminus\Commands\StructuredListTrait;
use Pantheon\TerminusCarbon\Model\Regions;
use Pantheon\Terminus\Commands\Site\SiteCommand;

class SiteRegionCommand extends SiteCommand
{
    use StructuredListTrait;

    /**
     * Displays carbon information about a site.
     *
     * @authorize
     *
     * @command carbon:info
     * @aliases site:carbon:info
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
     *
     * @default-fields id,name,upstream_machine_name,region,datacenter,cfe,grid_carbon_intensity
     *
     * @param string $site The name or UUID of a site to retrieve information on
     * @usage <site> Displays <site>'s information with carbon data.
     * @return PropertyList
     *
     */
    public function carbonInfo(string $site)
    {
        $site = $this->sites->get($site);
        $region = new Regions();
        $siteData = $region->mergeRegionData($site);
        return new PropertyList($siteData);
    }
}
