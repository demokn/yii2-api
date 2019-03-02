<?php

namespace demokn\api;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => Cors::class,
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/xml' => Response::FORMAT_XML,
                    // The reason for putting `json` to second position is
                    // let frontend developers specify `Accept` header themselves.
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
            'authenticator' => [
                'class' => CompositeAuth::class,
                'optional' => $this->authOptional(),
                'authMethods' => [
                    [
                        'class' => HttpHeaderAuth::class,
                        'header' => 'X-Access-Token',
                    ],
                ],
            ],
            'apiFilter' => [
                'class' => ApiFilter::class,
            ],
        ];
    }

    protected function verbs()
    {
        return [
            '*' => ['POST'],
        ];
    }

    protected function authOptional()
    {
        return [];
    }

    protected function validate(array $rules, array $data = null)
    {
        if (is_null($data)) {
            $data = Yii::$app->getRequest()->getBodyParams();
        }

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $message = '参数格式不正确';
            if (YII_DEBUG) {
                $message .= ': ' . $validator->errors()->first();
            }

            throw new ValidationException($message);
        }

        $results = [];
        foreach (array_keys($rules) as $key) {
            $results[$key] = data_get($data, $key);
        }

        return $results;
    }
}
