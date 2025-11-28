<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\ProgramForm;
use common\exceptions\ApiException;
use Yii;
use api\modules\v1\controllers\ApiController;

class ProgramController extends ApiController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => \kaabar\jwt\JwtHttpBearerAuth::class,
            'except' => [
                // 'index',
            ],
        ];

        return $behaviors;
    }

    // public function actionIndex()
    // {
    //     $programs = ProgramForm::findUserPrograms(Yii::$app->user->id);

    //     return [
    //         'success' => true,
    //         'message' => 'Programs retrived',
    //         'data' => [
    //             'programs' => $programs,
    //         ],
    //     ];
    // }

    // public function actionView($id)
    // {
    //     $model = ProgramForm::findUserProgram(Yii::$app->user->id, $id);

    //     if ($model == null) {
    //         throw new ApiException('TASK_DOESNT_EXIST');
    //     }

    //     return [
    //         'success' => true,
    //         'message' => 'Program retrived',
    //         'data' => [
    //             'program' => $model,
    //         ],
    //     ];
    // }

    public function actionCreate()
    {
        $model = new ProgramForm();

        $model->scenario = 'create';

        $model->load(['ProgramForm' => Yii::$app->request->post()]);
        if ($model->createProgram(Yii::$app->user->id)) {
            return [
                'success' => true,
                'message' => 'Program created successfully',
                'data' => [
                    'program' => $model,
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    // public function actionUpdate($id)
    // {
    //     $model = ProgramForm::findUserProgram(Yii::$app->user->id, $id);

    //     if ($model == null) {
    //         throw new ApiException('TASK_DOESNT_EXIST');
    //     }

    //     $model->scenario = 'update';

    //     $model->load(['ProgramForm' => Yii::$app->request->post()]);
    //     if ($model->updateProgram()) {
    //         return [
    //             'success' => true,
    //             'message' => 'Program updated successfully',
    //             'data' => [
    //                 'program' => $model,
    //             ],
    //         ];
    //     }

    //     //todo add unexpected error
    //     return [
    //         'success' => false,
    //         'message' => 'Failed to update program',
    //         'data' => $model->errors,
    //     ];
    // }

    // public function actionDelete($id)
    // {
    //     $model = ProgramForm::findUserProgram(Yii::$app->user->id, $id);

    //     if ($model == null) {
    //         throw new ApiException('TASK_DOESNT_EXIST');
    //     }

    //     $model->load(['ProgramForm' => Yii::$app->request->post()]);
    //     if ($model->deleteProgram()) {
    //         return [
    //             'success' => true,
    //             'message' => 'Program deleted successfully',
    //             'data' => [],
    //         ];
    //     }

    //     //todo add unexpected error
    //     return [
    //         'success' => false,
    //         'message' => 'Failed to delete program',
    //         'data' => $model->errors,
    //     ];
    // }

    // // todo remake for POST request
    // public function actionToggle($id)
    // {
    //     $model = ProgramForm::findUserProgram(Yii::$app->user->id, $id);

    //     if ($model == null) {
    //         throw new ApiException('TASK_DOESNT_EXIST');
    //     }

    //     $model->scenario = 'toggle';

    //     if ($model->toggleProgram()) {
    //         return [
    //             'success' => true,
    //             'message' => 'Program toggled successfully',
    //             'data' => [
    //                 'program' => $model
    //             ],
    //         ];
    //     }

    //     //todo add unexpected error
    //     return [
    //         'success' => false,
    //         'message' => 'Failed to toggle program',
    //         'data' => $model->errors,
    //     ];
    // }
}
