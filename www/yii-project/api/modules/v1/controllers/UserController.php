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
            if (Yii::$app->user->id != $id) {
                throw new ApiException('USER_CANT_MAKE_THIS_ACTION');
            }
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

    public function actionUpdatePassword()
    {
        $model = UserForm::findOne(Yii::$app->user->id);

        if (!$model) {
            throw new ApiException('USER_DOESNT_EXIST');
        }

        $model->scenario = 'update-password';

        $model->load(['UserForm' => Yii::$app->request->post()]);
        if ($model->updatePasswordUser()) {
            return [
                'success' => true,
                'message' => 'User password updated successfully',
                'data' => [],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    public function actionUpdateStatus($id)
    {
        $model = null;

        if (Yii::$app->user->identity->isRoleAdmin()) {
            if (Yii::$app->user->id == $id) {
                throw new ApiException('ANDMIN_CANT_CHANGE_ITSELF');
            }
            $model = UserForm::findOne($id);
        } else {
            throw new ApiException('USER_CANT_MAKE_THIS_ACTION');
        }

        if (!$model) {
            throw new ApiException('USER_DOESNT_EXIST');
        }

        $model->scenario = 'update-status';

        $model->load(['UserForm' => Yii::$app->request->post()]);
        if ($model->updateStatusUser()) {
            return [
                'success' => true,
                'message' => 'User status updated successfully',
                'data' => [],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    public function actionDelete($id)
    {
        $model = null;

        if (Yii::$app->user->identity->isRoleAdmin()) {
            if (Yii::$app->user->id == $id) {
                throw new ApiException('ANDMIN_CANT_CHANGE_ITSELF');
            }
            $model = UserForm::findOne($id);
        } else {
            if (Yii::$app->user->id != $id) {
                throw new ApiException('USER_CANT_MAKE_THIS_ACTION');
            }
            $model = UserForm::findOne(Yii::$app->user->id);
        }

        if (!$model) {
            throw new ApiException('USER_DOESNT_EXIST');
        }

        if ($model->isRoleAdmin()) {
            throw new ApiException('CANT_DELETE_ADMIN');
        }

        $model->scenario = 'delete';

        $model->load(['UserForm' => Yii::$app->request->post()]);
        if ($model->deleteUser()) {
            return [
                'success' => true,
                'message' => 'User deleted successfully',
                'data' => [],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

}
