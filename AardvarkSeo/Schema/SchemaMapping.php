<?php

namespace Statamic\Addons\AardvarkSeo\Schema;

use Spatie\SchemaOrg\Schema;
use Statamic\API\Entry;
use Statamic\API\File;
use Statamic\API\YAML;

class SchemaMapping
{
    const MAPPING_FILE = 'schema-mappings.yaml';

    public function __construct($mapping, $context)
    {
        $this->context = $context;
        $this->mapping = $mapping;
    }

    private function getMappingSettings()
    {
        $mappings_file = File::get(settings_path(self::MAPPING_FILE));
        $mappings = Yaml::parse($mappings_file)['mappings'];
        return $mappings[$this->mapping];
    }

    public function schema()
    {
        $schemaItems = [];
        $mappingSchema = $this->getMappingSettings()['schema'];
        foreach($mappingSchema as $schemaType => $mapping) {
            $schemaItems[] = $this->generateSchemaFromMapping($mapping, $this->context);
        }
        return $schemaItems;
    }

    private static function generateSchemaFromMapping($mapping, $context)
    {
        // Check that the given value is a valid schema type
        if(!empty($mapping['schema_type']) && !method_exists(new Schema(), $mapping['schema_type'])) {
            throw new \Exception('A valid schema type must be set');
        }

        $schema = forward_static_call(array('Spatie\\SchemaOrg\\Schema', $mapping['schema_type']));
        $fieldType = collect($mapping)->get('field_type', 'text');

        collect($mapping['fields'])->each(function($field, $property) use ($context, $mapping, $schema) {
            $field = collect($field);
            switch($field->get('field_type', 'text')) {
                case 'collection':
                    $items = collect($context[$field['field']])->map(function($item) {
                        $entry = Entry::find($item);
                        return $entry->data();
                    });
                    $schemadItems = $items->map(function($item) use ($property, $mapping) {
                        $sub_mapping = $mapping['fields'][$property]['fields'];
                        return self::generateSchemaFromMapping($sub_mapping, $item);
                    });
                    $schema->setProperty($property, $schemadItems);
                break;
                case 'text':
                default:
                    $schema->setProperty($property, $context[$field['field']]);
                break;
            }
        });

        return $schema;
    }

    private function getMultipleRelate($data, $fields) {
        die(print_r($data));
    }
}
