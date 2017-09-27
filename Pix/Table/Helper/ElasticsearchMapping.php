<?php

namespace Pix\Table\Helper;

trait ElasticsearchMapping
{
    public function getMapping()
    {
        $output_columns = array();
        foreach ($this->_columns as $name => $data) {
            switch ($data['type']) {
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
                $type = 'integer';
                $index = 'true';
                break;
            case 'bigint':
                $type = 'long';
                $index = 'true';
                break;
            case 'char':
            case 'varchar':
            case 'text':
                $type = 'string';
                $index = 'analyzed';
                break;
            }
            $output_columns[] = array(
                'name' => $name,
                'type' => $type,
                'index' => $index,
                'es_name' => $data['es_name'],
            );
        }
        return $output_columns;
    }
}
