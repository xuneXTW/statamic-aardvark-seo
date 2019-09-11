<?php

namespace Statamic\Addons\AardvarkSeo\Schema;

use Spatie\SchemaOrg\Schema;
use Statamic\Addons\AardvarkSeo\Schema\SchemaIds;
use Statamic\API\Config;
use Statamic\API\Content;
use Statamic\API\URL;

class Breadcrumbs implements SchemaPart
{
    /**
     * Similar to how NavTags->breadcrumbs works
     */
    public function list()
    {
        $crumbs = [];

        $url = URL::getCurrent();
        $locale = site_locale();

        $segments = explode('/', $url);
        $segment_count = count($segments);
        $segments[0] = '/';

        // Create crumbs from segments
        $segment_urls = [];
        for ($i = 1; $i <= $segment_count; $i++) {
            $segment_urls[] = URL::tidy(join($segments, '/'));
            array_pop($segments);
        }

        // Build up the content for each crumb
        foreach ($segment_urls as $segment_url) {
            $default_segment_uri = URL::getDefaultUri($locale, $segment_url);

            $content = Content::whereUri($default_segment_uri);

            if (! $content) {
                $content = app(\Statamic\Routing\Router::class)->getRoute($segment_url);
            }

            if (! $content) {
                continue;
            }

            if ($content instanceof \Statamic\Contracts\Data\Content\Content) {
                $content = $content->in($locale);
            }

            $crumbs[$segment_url] = $content->toArray();
            $crumbs[$segment_url]['is_current'] = (URL::getCurrent() == $segment_url);
        }

        return array_reverse($crumbs);
    }

    public function data()
    {
        $breadcrumbs = Schema::breadcrumbList();
        $crumbs = $this->list();

        $position = 1;
        $listItems = [];
        foreach($crumbs as $crumb) {
            $listItem = Schema::listItem();
            $listItem->position($position);
            $item = Schema::thing();
            $item->name($crumb['title']);
            $item->setProperty('@id', $crumb['permalink']);
            $listItem->item($item);
            $listItems[] = $listItem;
            $position++;
        }

        $breadcrumbs->itemListElement($listItems);
        return $breadcrumbs;
    }

    public static function id()
    {
        return Config::getSiteUrl() . SchemaIds::BREADCRUMBS;
    }
}
