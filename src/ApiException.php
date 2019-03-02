<?php

namespace demokn\api;

use yii\web\HttpException;

class ApiException extends HttpException
{
    public $responseCode;
    public $responseData;

    public function __construct($responseCode, $message = null, array $data = [], $statusCode = 400, $code = 0, \Exception $previous = null)
    {
        $this->responseCode = $responseCode;
        $this->responseData = $data;

        parent::__construct($statusCode, $message, $code, $previous);
    }
}
