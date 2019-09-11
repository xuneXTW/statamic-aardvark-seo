<?php

namespace Statamic\Addons\AardvarkSeo;
use Statamic\Extend\ServiceProvider;

class AardvarkSeoServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if(!class_exists('Spatie\\SchemaOrg\\Graph')) {
            throw new \Exception('Required dependencies missing for Aardvark SEO addon');
        }
    }

}
