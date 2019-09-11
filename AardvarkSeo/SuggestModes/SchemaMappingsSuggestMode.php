<?php

namespace Statamic\Addons\AardvarkSeo\SuggestModes;

use Statamic\Addons\AardvarkSeo\Schema\SchemaMapping;
use Statamic\Addons\Suggest\Modes\AbstractMode;
use Statamic\API\File;
use Statamic\API\YAML;

class SchemaMappingsSuggestMode extends AbstractMode
{
    /**
     * Return a list of schema mappings
     *
     * @return array
     */
    public function suggestions()
    {
        $mappings_file = File::get(settings_path(SchemaMapping::MAPPING_FILE));
        $mappings = Yaml::parse($mappings_file)['mappings'];
        $values = [];
        foreach($mappings as $item => $mapping) {
            $values[] = [
                'value' => $item,
                'text' => $mapping['title']
            ];
        }
        return $values;
    }
}
