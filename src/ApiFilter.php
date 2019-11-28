<?php

namespace demokn\api;

use Yii;
use yii\base\ActionFilter;
use yii\web\Response;

class ApiFilter extends ActionFilter
{
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        if ($result instanceof Response) {
            return $result;
        }

        $response = Yii::$app->getResponse();

        return [
            'code' => $response->statusCode,
            'data' => is_array($result) && count($result) === 0 ? new \stdClass() : $result,
            'message' => $response->statusText,
        ];
    }
}
