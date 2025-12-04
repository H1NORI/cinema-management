<?php

namespace api\modules\v1\controllers;

use api\modules\v1\controllers\ApiController;
use api\modules\v1\models\UserForm;
use common\exceptions\ApiException;
use Yii;

class UserController extends ApiController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => \kaabar\jwt\JwtHttpBearerAuth::class,
            'optional' => [
            ],
        ];

        return $behaviors;
    }

    public function actionUpdate($id)
    {
        $model = null;

        if (Yii::$app->user->identity->isRoleAdmin()) {
            $model = UserForm::findOne($id);
        } else {
            $model = UserForm::findOne(Yii::$app->user->id);
        }

        if (!$model) {
            throw new ApiException('USER_DOESNT_EXIST');
        }

        $model->scenario = 'update';

        $model->load(['UserForm' => Yii::$app->request->post()]);
        if ($model->updateUser()) {
            return [
                'success' => true,
                'message' => 'User updated successfully',
                'data' => [],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

}
