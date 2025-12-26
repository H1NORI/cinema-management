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
        $programs = ProgramForm::find()->all();

        $userId = Yii::$app->user->id;
        $isGuest = Yii::$app->user->isGuest;

        $result = [];

        foreach ($programs as $program) {
            $role = null;

            if (!$isGuest) {
                $userProgramRole = ProgramUserRoleForm::findProgramUserRole($userId, $program->id);
                $role = $userProgramRole?->role;
            }

            if ($role || $program->state === $program::STATE_ANNOUNCED) {
                $result[] = $program->toPublicArray($role);
            }
        }

        return [
            'success' => true,
            'message' => 'Programs retrived',
            'data' => [
                'programs' => $result,
            ],
        ];
    }

    public function actionSearch()
    {
        $model = new ProgramForm();
        $model->scenario = 'search';

        $programs = $model->search(['ProgramForm' => $this->request->queryParams]);

        $userId = Yii::$app->user->id;
        $isGuest = Yii::$app->user->isGuest;

        $result = [];

        foreach ($programs as $program) {
            $role = null;

            if (!$isGuest) {
                $userProgramRole = ProgramUserRoleForm::findProgramUserRole($userId, $program->id);
                $role = $userProgramRole?->role;
            }

            if ($role || $program->state === $program::STATE_ANNOUNCED) {
                $result[] = $program->toPublicArray($role);
            }
        }

        return [
            'success' => true,
            'message' => 'Programs retrived',
            'data' => [
                'programs' => $result,
            ],
        ];
    }

    public function actionView($id)
    {
        $program = ProgramForm::findOne($id);

        if ($program == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
        }

        $userRole = Yii::$app->user->id ? ProgramUserRoleForm::findProgramUserRole(Yii::$app->user->id, $id)?->role : null;

        if ($userRole || $program->state === $program::STATE_ANNOUNCED) {
            return [
                'success' => true,
                'message' => 'Program retrived',
                'data' => [
                    'program' => $program->toPublicArray($userRole),
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
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
        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        $model = ProgramForm::findUserProgram(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
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


    //todo add actionRemoveProgrammer
    public function actionAddProgrammer($id)
    {
        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        $model = ProgramForm::findProgram($id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
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

    //todo add actionRemoveStaff
    public function actionAddStaff($id)
    {
        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        $model = ProgramForm::findProgram($id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
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
        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        $model = ProgramForm::findProgram($id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
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
        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        $model = ProgramForm::findProgram($id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
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
