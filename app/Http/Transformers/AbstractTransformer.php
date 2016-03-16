<?php

namespace App\Http\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

abstract class AbstractTransformer extends TransformerAbstract
{
    /**
     * Transform all loaded relations for object (wrap relationship data into 
     * data array)
     *
     * @param array $data
     * @param Model $object
     *
     * @return array
     */
    protected function transformRelations(array $data, Model $object)
    {
        foreach (array_keys($object->getRelations()) as $relation) {
            $data[$relation] = ['data' => $object->$relation];
        }

        return $data;
    }
}
