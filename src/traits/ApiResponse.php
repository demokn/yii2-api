<?php

namespace demokn\api\traits;

use Yii;

trait ApiResponse
{
    /**
     * @var int
     */
    protected $statusCode = 200;


    protected function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    protected function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param $data
     * @param  array             $header
     * @return \yii\web\Response
     */
    protected function respond($data, $header = [])
    {
        $response = Yii::$app->getResponse();
        $response->data = is_array($data) && count($data) === 0 ? new \stdClass() : $data;
        foreach ($header as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }

    /**
     * @param  array             $data
     * @return \yii\web\Response
     */
    protected function success($data)
    {
        return $this->respond([
            'code' => 200,
            'data' => $data,
            'message' => 'OK',
        ]);
    }

    /**
     * @param $message
     * @param  int               $code
     * @param  array             $data
     * @return \yii\web\Response
     */
    protected function failed($message, $code = 400, $data = [])
    {
        return $this->respond([
            'code' => $code,
            'data' => $data,
            'message' => $message,
        ]);
    }

    /**
     * @param  string            $message
     * @return \yii\web\Response
     */
    protected function internalError($message = 'Internal Error')
    {
        return $this->failed($message, 500);
    }

    /**
     * @param  string            $message
     * @return \yii\web\Response
     */
    protected function notFound($message = 'Not Found')
    {
        return $this->failed($message, 404);
    }
}
