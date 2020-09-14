<?php

namespace demokn\api;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Yii;

class Validator
{
    /** @var Factory */
    protected $factory;

    protected static $instance;

    protected function init()
    {
        $this->factory = $this->createFactory();
    }

    protected function getDefaultTranslator()
    {
        return new Translator(new FileLoader(new Filesystem(), dirname(__FILE__).'/lang'), 'en');
    }

    protected function createFactory()
    {
        $translator = Yii::$container->has('validation.translator')
            ? Yii::$container->get('validation.translator')
            : $this->getDefaultTranslator();

        return new Factory($translator);
    }

    public static function getInstance()
    {
        if (!is_null(self::$instance)) {
            return self::$instance;
        }

        $instance = new self();
        $instance->init();

        return self::$instance = $instance;
    }

    /**
     * @return \Illuminate\Validation\Validator
     */
    public static function make(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        return self::getInstance()->factory->make($data, $rules, $messages, $customAttributes);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @return array
     */
    public static function validate(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        return self::getInstance()->factory->validate($data, $rules, $messages, $customAttributes);
    }
}
