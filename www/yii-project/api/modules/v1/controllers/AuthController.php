<?php

namespace api\modules\v1\controllers;

use api\modules\v1\controllers\ApiController;
use api\modules\v1\models\SigninForm;
use api\modules\v1\models\SignupForm;
use common\exceptions\ApiException;
use common\models\User;
use common\models\UserRefreshToken;
use Yii;

class AuthController extends ApiController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => \kaabar\jwt\JwtHttpBearerAuth::class,
            'except' => [
                'signin',
                'signup',
            ],
        ];

        return $behaviors;
    }

    public function actionSignin()
    {
        $model = new SigninForm();

        $model->scenario = 'signin';

        $model->load(['SigninForm' => Yii::$app->request->post()]);

        if ($model->signin()) {
            return [
                'success' => true,
                'message' => 'User signed in successfully',
                'data' => [
                    'user' => [
                        'id' => $model->getUserId(),
                        'email' => $model->getUserEmail(),
                        //TODO add more fields?
                    ],
                    'token' => $model->token,
                    'refresh_token' => $model->refreshToken,
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    public function actionSignup()
    {
        $model = new SignupForm();

        $model->scenario = 'signup';

        $model->load(['SignupForm' => Yii::$app->request->post()]);
        if ($model->signup()) {
            return [
                'success' => true,
                'message' => 'User signed up successfully',
                'data' => [],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    //TODO rework refresh-token action
    public function actionRefreshToken()
    {
        $refreshToken = Yii::$app->request->cookies->getValue('refresh-token', false);
        if (!$refreshToken) {
            return new \yii\web\UnauthorizedHttpException('No refresh token found.');
        }

        $userRefreshToken = UserRefreshToken::findOne(['urf_token' => $refreshToken]);

        if (Yii::$app->request->getMethod() == 'POST') {
            // Getting new JWT after it has expired
            if (!$userRefreshToken) {
                return new \yii\web\UnauthorizedHttpException('The refresh token no longer exists.');
            }

            $user = User::find()
                ->where(['id' => $userRefreshToken->urf_userID])
                ->one();

            if (!$user) {
                $userRefreshToken->delete();
                return new \yii\web\UnauthorizedHttpException('The user is inactive.');
            }

            $token = $this->generateJwt($user);

            return [
                'status' => true,
                'token' => (string) $token,
            ];

        } elseif (Yii::$app->request->getMethod() == 'DELETE') {
            // Logging out
            if ($userRefreshToken && !$userRefreshToken->delete()) {
                return new \yii\web\ServerErrorHttpException('Failed to delete the refresh token.');
            }

            return ['status' => 'ok'];
        } else {
            return new \yii\web\UnauthorizedHttpException('The user is inactive.');
        }
    }

}
