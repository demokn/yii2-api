<?php

namespace demokn\api;

use League\Fractal\Manager;
use Spatie\Fractalistic\ArraySerializer;
use yii\db\ActiveRecord;

class Fractal extends \Spatie\Fractalistic\Fractal
{
    protected $serializer = ArraySerializer::class;

    /**
     * @param null|mixed $data
     * @param null|string|callable|\League\Fractal\TransformerAbstract $transformer
     * @param null|string|\League\Fractal\Serializer\SerializerAbstract $serializer
     *
     * @return \Spatie\Fractalistic\Fractal
     */
    public static function create($data = null, $transformer = null, $serializer = null)
    {
        $instance = new static(new Manager());

        $instance->data = $data;
        $instance->dataType = $instance->determineDataType($data);
        if (!is_null($transformer)) {
            $instance->transformer = $transformer;
        }
        if (!is_null($serializer)) {
            $instance->serializer = $serializer;
        }

        return $instance;
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    protected function determineDataType($data)
    {
        if ($data instanceof ActiveRecord) {
            return 'item';
        }

        return parent::determineDataType($data);
    }
}
