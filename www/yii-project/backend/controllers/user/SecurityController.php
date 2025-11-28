<?php
namespace backend\controllers\user;

use Yii;
use yii\web\Response;
use dektrium\user\models\LoginForm;
use yii\widgets\ActiveForm;


class SecurityController extends \dektrium\user\controllers\SecurityController
{    

    public function actionLogin()
    {
        $this->layout = '@backend/views/layouts/blank';
        $model = Yii::createObject(LoginForm::className());

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model'  => $model,
            'module' => $this->module,
        ]);
    }
}