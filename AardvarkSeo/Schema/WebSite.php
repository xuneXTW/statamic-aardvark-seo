<?php

namespace Statamic\Addons\AardvarkSeo\Schema;

use Spatie\SchemaOrg\Schema;
use Statamic\API\Config;
use Statamic\Addons\AardvarkSeo\Schema\SchemaIds;
use Statamic\Addons\AardvarkSeo\Schema\SiteOwner;

class WebSite implements SchemaPart
{
    public function __construct($context = [])
    {
        $this->context = $context;
    }

    public function data()
    {
        $site = Schema::webSite();
        $site->url(Config::getSiteUrl());
        $site->setProperty('publisher', ['@id' => SiteOwner::id()]);
        $site->setProperty('@id', self::id());
        return $site;
    }

    public static function id()
    {
        return Config::getSiteUrl() . SchemaIds::WEB_SITE;
    }
}
