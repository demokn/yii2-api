<?php

namespace demokn\api;

use yii\base\InvalidConfigException;
use yii\log\Target;

/**
 * Class LogStreamTarget.
 *
 * Example config
 * ```php
 * [
 *      'targets' => [
 *          [
 *              'class'  => 'demokn\api\LogStreamTarget',
 *              'levels' => ['error', 'warning'],
 *              'stream' => 'php://stderr',
 *              'except' => ['yii\web\HttpException:404']
 *          ],
 *          [
 *              'class'  => 'demokn\api\LogStreamTarget',
 *              'levels' => ['info'],
 *              'stream' => 'php://stdout',
 *          ],
 *      ],
 * ]
 * ```
 */
class LogStreamTarget extends Target
{
    /**
     * @var string
     * @link http://php.net/manual/zh/wrappers.php
     */
    public $stream = 'php://stdout';

    protected $resource;

    public function init()
    {
        if (empty($this->stream)) {
            throw new InvalidConfigException('No stream configured.');
        }
        if (($this->resource = @fopen($this->stream, 'w')) === false) {
            throw new InvalidConfigException("Unable to append to '{$this->stream}'");
        }
    }

    public function export()
    {
        foreach ($this->messages as $message) {
            fwrite($this->resource, $this->formatMessage($message).PHP_EOL);
        }
    }
}
