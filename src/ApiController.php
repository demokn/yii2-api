<?php

namespace demokn\api;

use demokn\api\traits\ApiResponse;
use Yii;
use yii\base\UserException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    use ApiResponse;

    public $enableCsrfValidation = false;

    public $displayValidationErrors = YII_DEBUG;

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
                    // to let frontend developers specify `Accept` header themselves.
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

    protected function formatValidationFailsMessage(\Illuminate\Validation\Validator $validator)
    {
        $message = '参数格式不正确';
        if ($this->displayValidationErrors) {
            $message .= ': '.$validator->errors()->first();
        }

        return $message;
    }

    protected function handleValidationFails(\Illuminate\Validation\Validator $validator)
    {
        throw new UserException($this->formatValidationFailsMessage($validator));
    }

    protected function getAllRequestParams()
    {
        return Yii::$app->getRequest()->getBodyParams()
            + Yii::$app->getRequest()->getQueryParams();
    }

    protected function validate(array $rules, array $data = null, array $customAttributes = [], array $messages = [])
    {
        if (is_null($data)) {
            $data = $this->getAllRequestParams();
        }

        $validator = Validator::make($data, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            $this->handleValidationFails($validator);
        }

        return $validator->validated();
    }
}
