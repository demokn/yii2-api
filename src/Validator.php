<?php

namespace demokn\api;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;

class Validator
{
    /** @var Factory */
    protected $factory;

    protected static $instance;

    protected function init()
    {
        $this->factory = $this->createFactory();
    }

    protected function createFactory()
    {
        $fileLoader = new FileLoader(new Filesystem(), dirname(__FILE__) . '/lang');
        $fileLoader->load('en', 'validation');
        $translator = new Translator($fileLoader, 'en');

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
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return \Illuminate\Validation\Validator
     */
    public static function make(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        return self::getInstance()->factory->make($data, $rules, $messages, $customAttributes);
    }

    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function validate(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        return self::getInstance()->factory->validate($data, $rules, $messages, $customAttributes);
    }
}
