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
        if (Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();
            // reset parameters of response to avoid interference with partially created response data
            // in case the error occurred while sending the response.
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
        } else {
            $response = new Response();
        }

        if ($this->enableRestStatusCode) {
            $response->setStatusCodeByException($exception);
        }

        $useErrorView = $response->format === Response::FORMAT_HTML && (!YII_DEBUG || $exception instanceof UserException);

        if ($useErrorView && $this->errorAction !== null) {
            $result = Yii::$app->runAction($this->errorAction);
            if ($result instanceof Response) {
                $response = $result;
            } else {
                $response->data = $result;
            }
        } elseif ($response->format === Response::FORMAT_HTML) {
            if ($this->shouldRenderSimpleHtml()) {
                // AJAX request
                $response->data = '<pre>'.$this->htmlEncode(static::convertExceptionToString($exception)).'</pre>';
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

        $response->send();
    }

    protected function convertExceptionToArray($exception)
    {
        if (!YII_DEBUG && !$exception instanceof UserException && !$exception instanceof HttpException) {
            $exception = new HttpException(500, Yii::t('yii', 'An internal server error occurred.'));
        }

        $array = [
            'code' => 500,
            'data' => [],
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof ApiException) {
            $array['code'] = $exception->responseCode;
            $array['data'] = $exception->responseData;
        } elseif ($exception instanceof HttpException) {
            $array['code'] = $exception->statusCode;
        } elseif ($exception instanceof UserException) {
            $array['code'] = 400;
        }

        // Convert empty array `data` to stdClass,
        // to force response `data` is always a class(especially in json format).
        if (is_array($array['data']) && count($array['data']) === 0) {
            $array['data'] = new \stdClass();
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
            $array['debug'] = $debugInfo;
        }

        return $array;
    }
}
