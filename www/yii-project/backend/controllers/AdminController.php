<?php

namespace backend\controllers;

use dektrium\user\controllers\AdminController as BaseAdminController;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;
use dektrium\user\models\UserSearch;
use yii\filters\AccessControl;
use common\models\User;



class AdminController extends BaseAdminController
{

    public function behaviors() {
        return [ 
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->identity->getIsAdmin();
                        }
                    ],
                ]
            ]
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


    public function beforeAction($action)
    {
        if ($action->id === 'error') {
            $this->layout = '@backend/views/layouts/blank'; 
        }
        return parent::beforeAction($action);
    }

    // protected function performAjaxValidation($models)
    // {
    //         \Yii::$app->response->format = Response::FORMAT_JSON;

    //         if (is_array($models)) {
    //             $result = [];
    //             foreach ($models as $model) {
    //                 if ($model->load(\Yii::$app->request->post())) {
    //                     $result = array_merge($result, ActiveForm::validate($model));
    //                 }
    //             }
    //             return $result;
    //         } else {
    //             if ($models->load(\Yii::$app->request->post())) {
    //                 return ActiveForm::validate($models);
    //             }
    //         }
            
    //         return [];
    // }

    public function actionIndex()
    {
        $searchModel  = \Yii::createObject(UserSearch::className());
        $dataProvider = $searchModel->search($_GET);

        return $this->render('@app/views/admin/index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    public function actionCreate()
    {
        $user = \Yii::createObject([
            'class'    => User::className(),
            'scenario' => 'create',
        ]);

        if (Yii::$app->request->isAjax && $user->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($user);
        }

        if ($user->load(Yii::$app->request->post()) && $user->create()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been created'));
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'user' => $user
        ]);
    }

    public function actionUpdate($id)
    {
        $user = $this->findModel($id);
        $user->scenario = 'update';
        $profile = $this->finder->findProfileById($id);
        $request = \Yii::$app->request;

        if ($request->isAjax && $user->load($request->post()) && $profile->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return array_merge(
                ActiveForm::validate($user),
                ActiveForm::validate($profile)
            );
        }

        if ($user->load($request->post()) && $profile->load($request->post())) {
            $valid = $user->validate() && $profile->validate();

            if ($valid) {
                $user->save(false);
                $profile->save(false);
                Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been updated'));
                return $this->refresh();
            }
        }

        return $this->render('update', [
            'user'    => $user,
            'profile' => $profile,
            'module'  => $this->module,
        ]);
    }

    public function actionView($id) {
        $model = $this->findModel($id);
        // $model->scenario = 'view';
        $profile = $this->finder->findProfileById($id);

        return $this->render('@backend/views/admin/view', [
            'model'    => $model,
            'profile' => $profile,
            'module'  => $this->module,
        ]);
    }


}
