<?php

namespace backend\controllers;

use common\models\UserRefreshToken;
use backend\models\search\UserRefreshTokenSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RefreshTokenController implements the CRUD actions for UserRefreshToken model.
 */
class RefreshTokenController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all UserRefreshToken models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserRefreshTokenSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pageSize' => $searchModel->pageSize,
        ]);
    }

    /**
     * Displays a single UserRefreshToken model.
     * @param int $user_refresh_tokenID User Refresh Token ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    // public function actionView($user_refresh_tokenID)
    // {
    //     return $this->render('view', [
    //         'model' => $this->findModel($user_refresh_tokenID),
    //     ]);
    // }

    /**
     * Creates a new UserRefreshToken model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    // public function actionCreate()
    // {
    //     $model = new UserRefreshToken();

    //     if ($this->request->isPost) {
    //         if ($model->load($this->request->post()) && $model->save()) {
    //             return $this->redirect(['view', 'user_refresh_tokenID' => $model->user_refresh_tokenID]);
    //         }
    //     } else {
    //         $model->loadDefaultValues();
    //     }

    //     return $this->render('create', [
    //         'model' => $model,
    //     ]);
    // }

    /**
     * Updates an existing UserRefreshToken model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $user_refresh_tokenID User Refresh Token ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    // public function actionUpdate($user_refresh_tokenID)
    // {
    //     $model = $this->findModel($user_refresh_tokenID);

    //     if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
    //         return $this->redirect(['view', 'user_refresh_tokenID' => $model->user_refresh_tokenID]);
    //     }

    //     return $this->render('update', [
    //         'model' => $model,
    //     ]);
    // }

    /**
     * Deletes an existing UserRefreshToken model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $user_refresh_tokenID User Refresh Token ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($user_refresh_tokenID)
    {
        $this->findModel($user_refresh_tokenID)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserRefreshToken model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $user_refresh_tokenID User Refresh Token ID
     * @return UserRefreshToken the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($user_refresh_tokenID)
    {
        if (($model = UserRefreshToken::findOne(['user_refresh_tokenID' => $user_refresh_tokenID])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
