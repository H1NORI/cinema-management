<?php

namespace api\modules\v1\controllers;

use api\modules\v1\controllers\ApiController;
use api\modules\v1\models\SigninForm;
use api\modules\v1\models\SignupForm;
use common\exceptions\ApiException;
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
    //todo add error if already loginned

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
                        'token' => $model->token,
                        //TODO add more fields?
                    ],
                    'refresh_token' => $model->refreshToken,
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }
    
    //todo add error if already loginned
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

    public function actionLogout($id)
    {
        $model = new SigninForm();

        if (!$id) {
            $id = Yii::$app->user->id;
        } else if (!Yii::$app->user->identity->isRoleAdmin() && Yii::$app->user->id != $id) {
            throw new ApiException('USER_CANT_LOGUT_SOMEONE');
        }

        $model->scenario = 'logout';

        $model->load(['SigninForm' => Yii::$app->request->post()]);
        if ($model->logout($id)) {
            return [
                'success' => true,
                'message' => 'User logged out successfully',
                'data' => [],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    //TODO rework refresh-token action
    // public function actionRefreshToken()
    // {
    //     $refreshToken = Yii::$app->request->cookies->getValue('refresh-token', false);
    //     if (!$refreshToken) {
    //         return new \yii\web\UnauthorizedHttpException('No refresh token found.');
    //     }

    //     $userRefreshToken = UserRefreshToken::findOne(['urf_token' => $refreshToken]);

    //     if (Yii::$app->request->getMethod() == 'POST') {
    //         // Getting new JWT after it has expired
    //         if (!$userRefreshToken) {
    //             return new \yii\web\UnauthorizedHttpException('The refresh token no longer exists.');
    //         }

    //         $user = User::find()
    //             ->where(['id' => $userRefreshToken->urf_userID])
    //             ->one();

    //         if (!$user) {
    //             $userRefreshToken->delete();
    //             return new \yii\web\UnauthorizedHttpException('The user is inactive.');
    //         }

    //         $token = $this->generateJwt($user);

    //         return [
    //             'status' => true,
    //             'token' => (string) $token,
    //         ];

    //     } elseif (Yii::$app->request->getMethod() == 'DELETE') {
    //         // Logging out
    //         if ($userRefreshToken && !$userRefreshToken->delete()) {
    //             return new \yii\web\ServerErrorHttpException('Failed to delete the refresh token.');
    //         }

    //         return ['status' => 'ok'];
    //     } else {
    //         return new \yii\web\UnauthorizedHttpException('The user is inactive.');
    //     }
    // }

}
