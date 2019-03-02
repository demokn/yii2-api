<?php

namespace demokn\api;

use Yii;
use yii\helpers\VarDumper;
use yii\log\Logger;

class Log
{
    protected static function log($level, array $args = [])
    {
        if (count($args) === 0) {
            throw new \InvalidArgumentException("Message can not be empty.");
        }

        foreach ($args as &$arg) {
            if (!is_string($arg)) {
                // exceptions may not be serializable if in the call stack somewhere is a Closure
                if ($arg instanceof \Throwable || $arg instanceof \Exception) {
                    $arg = (string) $arg;
                } else {
                    $arg = VarDumper::export($arg);
                }
            }
        }

        $message = count($args) === 1 ? reset($args) : call_user_func_array("sprintf", $args);

        Yii::getLogger()->log($message, $level);
    }

    public static function error()
    {
        self::log(Logger::LEVEL_ERROR, func_get_args());
    }

    public static function warning()
    {
        self::log(Logger::LEVEL_WARNING, func_get_args());
    }

    public static function info()
    {
        self::log(Logger::LEVEL_INFO, func_get_args());
    }

    public static function debug()
    {
        self::log(Logger::LEVEL_TRACE, func_get_args());
    }
}
