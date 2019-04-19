<?php

if (!function_exists('app')) {
    /**
     * @param null $component
     * @return mixed|\yii\console\Application|\yii\web\Application
     */
    function app($component = null)
    {
        if (is_null($component)) {
            return \Yii::$app;
        }

        return \Yii::$app->{$component};
    }
}

if (!function_exists('make')) {
    /**
     * @param null $abstract
     * @return object|\yii\di\Container
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    function make($abstract = null)
    {
        if (is_null($abstract)) {
            return \Yii::$container;
        }

        return \Yii::$container->get($abstract);
    }
}

if (!function_exists('validator')) {
    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return \Illuminate\Validation\Validator
     */
    function validator(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        return \demokn\api\Validator::make($data, $rules, $messages, $customAttributes);
    }
}

if (!function_exists('fractal')) {
    /**
     * @param null|mixed $data
     * @param null|string|callable|\League\Fractal\TransformerAbstract $transformer
     * @param null|string|\League\Fractal\Serializer\SerializerAbstract $serializer
     *
     * @return \Spatie\Fractalistic\Fractal
     */
    function fractal($data = null, $transformer = null, $serializer = null)
    {
        return \demokn\api\Fractal::create($data, $transformer, $serializer);
    }
}
