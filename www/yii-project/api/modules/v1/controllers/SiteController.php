<?php

namespace api\modules\v1\controllers;

use Yii;
use api\modules\v1\controllers\ApiController;
use yii\web\HttpException;

class SiteController extends ApiController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => \kaabar\jwt\JwtHttpBearerAuth::class,
            'except' => [
                'index',
                // 'test-error',
                'token',
            ],
        ];

        return $behaviors;
    }
    public function actionIndex()
    {
        return [
            'success' => true,
            'message' => 'API is working',
        ];
    }

    public function actionTest()
    {
        return [
            'success' => true,
            'message' => 'API is working',
        ];
    }

    public function actionTestError()
    {
        throw new HttpException(400, 'Something went wrong', 90001);
    }

    //TODO added for test purpose only
    public function actionToken()
    {
        $headers = Yii::$app->request->headers;
        $authHeader = $headers->get(name: 'Authorization');

        if (!$authHeader || !preg_match('/^Bearer\s+(.*)$/', $authHeader, $matches)) {
            return ['success' => false, 'message' => 'Authorization header missing or invalid', 'header' => $authHeader];
        }

        $tokenString = $matches[1];

        try {
            $jwt = Yii::$app->jwt;
            $token = $jwt->loadToken((string) $tokenString);

            return [
                'success' => true,
                'message' => 'Token parsed successfully',
                'header' => $authHeader,
                'token' => $token,
                'received_token' => $tokenString,
                'claims' => $token && !is_string($token)? $token->claims()->all() : $token,
                'jwt_key' => Yii::$app->jwt->key,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Token parsing failed',
                'error' => $e->getMessage(),
            ];
        }
    }

}
