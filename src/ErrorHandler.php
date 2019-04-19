<?php

namespace demokn\api;

use Yii;
use yii\base\UserException;
use yii\web\HttpException;
use yii\web\Response;

class ErrorHandler extends \yii\web\ErrorHandler
{
    public $enableRestStatusCode = false;

    protected function renderException($exception)
    {
        $response = Yii::$app->getResponse();
        // reset parameters of response to avoid interference with partially created response data
        // in case the error occurred while sending the response.
        $response->isSent = false;
        $response->stream = null;
        $response->data = null;
        $response->content = null;

        $useErrorView = ($response->format === Response::FORMAT_HTML) && ($exception instanceof UserException);
        if ($useErrorView && $this->errorAction !== null) {
            $result = Yii::$app->runAction($this->errorAction);
            if ($result instanceof Response) {
                $response = $result;
            } else {
                $response->data = $result;
            }
        } elseif ($response->format === Response::FORMAT_HTML) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                // AJAX request
                $response->data = '<pre>' . $this->htmlEncode(static::convertExceptionToString($exception)) . '</pre>';
            } else {
                // if there is an error during error rendering it's useful to
                // display PHP error in debug mode instead of a blank screen
                if (YII_DEBUG) {
                    ini_set('display_errors', 1);
                }
                $file = $useErrorView ? $this->errorView : $this->exceptionView;
                $response->data = $this->renderFile($file, [
                    'exception' => $exception,
                ]);
            }
        } elseif ($response->format === Response::FORMAT_RAW) {
            $response->data = static::convertExceptionToString($exception);
        } else {
            $response->data = $this->convertExceptionToArray($exception);
        }

        if ($this->enableRestStatusCode) {
            $statusCode = $exception instanceof HttpException ? $exception->statusCode : 500;
            $response->setStatusCode($statusCode);
        }

        $response->send();
    }

    protected function convertExceptionToArray($exception)
    {
        if (!(YII_DEBUG || $exception instanceof UserException)) {
            $exception = new HttpException(500, Yii::t('yii', 'An internal server error occurred.'));
        }

        $response = [
            'code' => 500,
            'data' => [],
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof ApiException) {
            $response['code'] = $exception->responseCode;
            $response['data'] = $exception->responseData;
        } elseif ($exception instanceof HttpException) {
            $response['code'] = $exception->statusCode;
        } elseif ($exception instanceof UserException) {
            $response['code'] = 400;
        }

        if (is_array($response['data']) && count($response['data']) === 0) {
            $response['data'] = new \stdClass();
        }

        if (YII_DEBUG) {
            $debugInfo = [
                'type' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'stack-trace' => explode("\n", $exception->getTraceAsString()),
            ];
            if ($exception instanceof \yii\db\Exception) {
                $debugInfo['error-info'] = $exception->errorInfo;
            }
            if (($prev = $exception->getPrevious()) !== null) {
                $debugInfo['previous'] = $this->convertExceptionToArray($prev);
            }
            $response['debug'] = $debugInfo;
        }

        return $response;
    }
}
