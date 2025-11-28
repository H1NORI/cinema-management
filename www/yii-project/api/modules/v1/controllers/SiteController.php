<?php

namespace api\modules\v1\controllers;

use Yii;
use api\modules\v1\controllers\ApiController;
use yii\web\HttpException;

class SiteController extends ApiController
{
    public function behaviors() {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => \kaabar\jwt\JwtHttpBearerAuth::class,
            'except' => [
                'index',
                // 'test-error',
                // 'test',
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
    public function actionToken() {
        $headers = Yii::$app->request->headers;
        $authHeader = $headers->get('Authorization');

        if (!$authHeader || !preg_match('/^Bearer\s+(.*)$/', $authHeader, $matches)) {
            return ['success' => false, 'message' => 'Authorization header missing or invalid'];
        }

        $tokenString = $matches[1];

        try {
            $jwt = Yii::$app->jwt;
            $token = $jwt->loadToken((string) $tokenString);

            return [
                'success' => true,
                'message' => 'Token parsed successfully',
                'token' => $token,
                'claims' => $token->claims()->all(),
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
