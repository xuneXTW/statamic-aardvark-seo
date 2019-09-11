<?php

namespace Statamic\Addons\AardvarkSeo\Schema;

use Spatie\SchemaOrg\Schema;
use Statamic\API\Config;
use Statamic\API\URL;
use Statamic\Addons\AardvarkSeo\Schema\SchemaIds;
use Statamic\Addons\AardvarkSeo\Schema\WebSite;

class WebPage implements SchemaPart
{
    public function __construct($context = [])
    {
        $this->context = $context;
    }

    public function data()
    {
        $page = Schema::webPage();
        $page->setProperty('@id', self::id());
        $page->url(URL::makeAbsolute(URL::getCurrent()));
        $title = $this->context->get('meta_title') ?: $this->context->get('calculated_title', '');
        $page->name($title);
        $page->isPartOf(['@id' => WebSite::id()]);
        $page->inLanguage(Config::getFullLocale());
        $page->datePublished(date('c', $this->context->get('last_modified')));
        $page->dateModified(date('c', $this->context->get('last_modified')));
        return $page;
    }

    public static function id()
    {
        return Config::getSiteUrl() . SchemaIds::WEB_PAGE;
    }
}
