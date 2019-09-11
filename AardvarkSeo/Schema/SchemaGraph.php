<?php


namespace Statamic\Addons\AardvarkSeo\Schema;

use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\Schema;
use Statamic\Addons\AardvarkSeo\Schema\Breadcrumbs;
use Statamic\Addons\AardvarkSeo\Schema\SiteOwner;
use Statamic\Addons\AardvarkSeo\Schema\SchemaIds;
use Statamic\Addons\AardvarkSeo\Schema\WebPage;
use Statamic\Addons\AardvarkSeo\Schema\WebSite;
use Statamic\API\Config;

class SchemaGraph
{
    public function __construct($globals, $context)
    {
        $this->context = $context;
        $this->globals = $globals;
        $this->graph = new Graph();
        $this->populateData();
    }

    private function populateData()
    {

        $siteOwner = new SiteOwner($this->globals->get('company_or_person', 'company'), $this->globals);
        $webSite = new WebSite($this->globals);
        $webPage = new WebPage($this->context);
        $webPageData = $webPage->data();

        // // If breadcrumbs are enabled - add them to the graph
        if(!empty($this->globals->get('enable_breadcrumbs', 0)) && $this->context->get('url', '') !== '/') {
            $breadcrumbs = new Breadcrumbs();
            $webPageData->breadcrumb($breadcrumbs->data());
        }

        $this->graph->add($siteOwner->data());
        $this->graph->add($webSite->data());
        $this->graph->add($webPageData);

        $supplementarySchema = $this->getSupplementarySchema();
        foreach($supplementarySchema as $addition) {
            $items = $addition->schema();
            foreach($items as $item) {
                $this->graph->add($item);
            }
        }
    }

    private function getSupplementarySchema()
    {
        $supplementarySchema = $this->context->get('schema_mappings');

        $schema = [];

        if(!empty($supplementarySchema)) {
            foreach($supplementarySchema as $mapping) {
                $schema[] = new SchemaMapping($mapping, $this->context);
            }
        }

        return $schema;
    }

    public function render()
    {
        return $this->graph;
    }
}
