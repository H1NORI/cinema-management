<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\ProgramForm;
use api\modules\v1\models\ProgramUserRoleForm;
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
            'optional' => [
                'index',
                'search',
                'view',
            ],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        $programs = ProgramForm::findUserPrograms(Yii::$app->user->id);

        return [
            'success' => true,
            'message' => 'Programs retrived',
            'data' => [
                'programs' => $programs,
            ],
        ];
    }

    public function actionSearch()
    {
        $model = new ProgramForm();
        $model->scenario = 'search';
        
        $programs = $model->search(['ProgramForm' => $this->request->queryParams]);

        return [
            'success' => true,
            'message' => 'Programs retrived',
            'data' => [
                'programs' => $programs,
            ],
        ];
    }

    public function actionView($id)
    {
        $model = ProgramForm::findOne($id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
        }

        $userRole = Yii::$app->user->id ? ProgramUserRoleForm::findProgramUserRole(Yii::$app->user->id, $id)->role : null;

        return [
            'success' => true,
            'message' => 'Program retrived',
            'data' => [
                'program' => $model->toPublicArray($userRole),
            ],
        ];
    }

    public function actionCreate()
    {
        $model = new ProgramForm();

        $model->scenario = 'create';

        if (Yii::$app->user->identity->isRoleAdmin()) {
            throw new ApiException('ADMIN_CANT_MANAGE_PROGRAM');
        }

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

    public function actionUpdate($id)
    {
        $model = ProgramForm::findUserProgram(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
        }

        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        $model->scenario = 'update';

        $model->load(['ProgramForm' => Yii::$app->request->post()]);
        if ($model->updateProgram()) {
            return [
                'success' => true,
                'message' => 'Program updated successfully',
                'data' => [
                    'program' => $model,
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }


    public function actionAddProgrammer($id)
    {
        $model = ProgramForm::findUserProgram(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
        }

        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        $model->scenario = 'add-programmer';

        $model->load(['ProgramForm' => Yii::$app->request->post()]);
        if ($model->addProgrammer()) {
            return [
                'success' => true,
                'message' => 'Program programmer added successfully',
                'data' => [
                    'program' => $model,
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    public function actionAddStaff($id)
    {
        $model = ProgramForm::findUserProgram(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
        }

        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        $model->scenario = 'add-staff';

        $model->load(['ProgramForm' => Yii::$app->request->post()]);
        if ($model->addStaff()) {
            return [
                'success' => true,
                'message' => 'Program staff added successfully',
                'data' => [
                    'program' => $model,
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    public function actionDelete($id)
    {
        $model = ProgramForm::findUserProgram(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
        }

        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        $model->load(['ProgramForm' => Yii::$app->request->post()]);
        if ($model->deleteProgram()) {
            return [
                'success' => true,
                'message' => 'Program deleted successfully',
                'data' => [],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }


    public function actionUpdateState($id)
    {
        $model = ProgramForm::findUserProgram(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
        }

        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        if ($model->updateState()) {
            return [
                'success' => true,
                'message' => 'Program state updated successfully',
                'data' => [
                    'program' => $model,
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }


}
