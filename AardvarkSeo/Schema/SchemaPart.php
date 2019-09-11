<?php

namespace Statamic\Addons\AardvarkSeo\Schema;

use Spatie\SchemaOrg\Schema;

interface SchemaPart {

    public function data();

    public static function id();

}
