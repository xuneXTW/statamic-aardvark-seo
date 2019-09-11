<?php

namespace Statamic\Addons\AardvarkSeo\Schema;

use Spatie\SchemaOrg\Schema;
use Statamic\API\Asset;
use Statamic\API\Config;
use Statamic\Addons\AardvarkSeo\Schema\SchemaIds;

class SiteOwner implements SchemaPart
{
    public function __construct($type = 'company', $context = [])
    {
       $this->type = $type;
       $this->context = $context;
    }

    public function data()
    {
        if($this->type === 'company') {
            $owner = Schema::organization();
            $owner->name($this->context->get('company_name', ''));
            $logo = $this->context->get('company_logo', '');
            if(!empty($logo)) {
                $logoAsset = Asset::find($logo);
                $logoObject = Schema::imageObject();
                $logoObject->url($logoAsset->absoluteUrl());
                $logoObject->width($logoAsset->width());
                $logoObject->height($logoAsset->height());
                $owner->logo($logoObject);
            }
        } else {
            $owner = Schema::person();
            $owner->name($this->context->get('your_name', ''));
        }
        $owner->setProperty('@id', self::id());
        $owner->url(Config::getSiteUrl());
        if(!empty($this->context->get('social_links'))) {
            $owner->sameAs(
                collect($this->context->get('social_links'))
                    ->map(function($link) {
                        return $link['url'];
                    })
                    ->toArray()
            );
        }
        return $owner;
    }

    /**
     * Return the ID of the site owner
     */
    public static function id()
    {
        return Config::getSiteUrl() . SchemaIds::SITE_OWNER;
    }
}
