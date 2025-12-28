<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\ProgramUserRoleForm;
use api\modules\v1\models\ScreeningForm;
use common\exceptions\ApiException;
use Yii;
use api\modules\v1\controllers\ApiController;

class ScreeningController extends ApiController
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

    //todo review if it works right(I dont think role check works right)
    public function actionIndex($program_id)
    {
        $screenings = ScreeningForm::find()->where(['program_id' => $program_id])->all();

        $userId = Yii::$app->user->id;
        $isGuest = Yii::$app->user->isGuest;

        $result = [];

        foreach ($screenings as $screening) {
            $role = null;

            if (!$isGuest) {
                $userProgramRole = ProgramUserRoleForm::findProgramUserRole($userId, $screening->program_id);
                $role = $userProgramRole?->role;
            }

            if ($role || $screening->state === $screening::STATE_SCHEDULED) {
                $result[] = $screening->toPublicArray($role);
            }
        }

        return [
            'success' => true,
            'message' => 'Screenings retrived',
            'data' => [
                'screenings' => $result,
            ],
        ];
    }

    public function actionSearch()
    {
        $model = new ScreeningForm();
        $model->scenario = 'search';

        $screenings = $model->search(['ScreeningForm' => $this->request->queryParams]);

        $userId = Yii::$app->user->id;
        $isGuest = Yii::$app->user->isGuest;

        $result = [];

        foreach ($screenings as $screening) {
            $role = null;

            if (!$isGuest) {
                $userProgramRole = ProgramUserRoleForm::findProgramUserRole($userId, $screening->program_id);
                $role = $userProgramRole?->role;
            }

            if ($role || $screening->state === $screening::STATE_SCHEDULED) {
                $result[] = $screening->toPublicArray($role);
            }
        }

        return [
            'success' => true,
            'message' => 'Screenings retrieved',
            'data' => [
                'screenings' => $result,
            ],
        ];
    }

    public function actionView($id)
    {
        $model = ScreeningForm::findOne($id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
        }

        $userRole = Yii::$app->user->id ? ProgramUserRoleForm::findProgramUserRole(Yii::$app->user->id, $model->program_id)?->role : null;

        return [
            'success' => true,
            'message' => 'Screening retrived',
            'data' => [
                'screening' => $model->toPublicArray($userRole),
            ],
        ];
    }

    public function actionCreate()
    {
        $model = new ScreeningForm();

        $model->scenario = 'create';

        if (Yii::$app->user->identity->isRoleAdmin()) {
            throw new ApiException('ADMIN_CANT_MANAGE_SCREENING');
        }

        $model->load(['ScreeningForm' => Yii::$app->request->post()]);
        if ($model->createScreening(Yii::$app->user->id)) {
            return [
                'success' => true,
                'message' => 'Screening created successfully',
                'data' => [
                    'screening' => $model,
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    public function actionUpdate($id)
    {
        $model = ScreeningForm::findSubmitterScreening(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('SCREENING_DOESNT_EXIST');
        }

        $model->scenario = 'update';

        $model->load(['ScreeningForm' => Yii::$app->request->post()]);
        if ($model->updateScreening()) {
            return [
                'success' => true,
                'message' => 'Screening updated successfully',
                'data' => [
                    'screening' => $model,
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    public function actionSubmit($id)
    {
        $model = ScreeningForm::findSubmitterScreening(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('SCREENING_DOESNT_EXIST');
        }

        $model->scenario = 'submit';

        if ($model->submitScreening()) {
            return [
                'success' => true,
                'message' => 'Screening submitted successfully',
                'data' => [
                    'screening' => $model,
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    public function actionWithdraw($id)
    {
        $model = ScreeningForm::findSubmitterScreening(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('SCREENING_DOESNT_EXIST');
        }

        $model->scenario = 'withdraw';

        if ($model->withdrawScreening()) {
            return [
                'success' => true,
                'message' => 'Screening withdrawed successfully',
                'data' => [
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    public function actionAssignHandler($id)
    {
        $model = ScreeningForm::findScreening($id);

        if ($model == null) {
            throw new ApiException('SCREENING_DOESNT_EXIST');
        }

        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $model->program_id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        $model->scenario = 'assign-handler';

        $model->load(['ScreeningForm' => Yii::$app->request->post()]);
        if ($model->assignScreeningHandler()) {
            return [
                'success' => true,
                'message' => 'Screening submitted successfully',
                'data' => [
                    'screening' => $model,
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }


    public function actionReview($id)
    {
        $model = ScreeningForm::findHandlerScreening(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
        }

        $model->scenario = 'review';

        $model->load(['ScreeningForm' => Yii::$app->request->post()]);
        if ($model->reviewScreening()) {
            return [
                'success' => true,
                'message' => 'Screening review added successfully',
                'data' => [
                    'screening' => $model,
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    public function actionApprove($id)
    {
        $model = ScreeningForm::findSubmitterScreening(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
        }

        $model->scenario = 'approve';

        if ($model->approveScreening()) {
            return [
                'success' => true,
                'message' => 'Screening approved successfully',
                'data' => [
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    public function actionReject($id)
    {
        //todo now only submitter can access it, not other PROGRAMMERS
        $model = ScreeningForm::findSubmitterScreening(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
        }

        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        $model->scenario = 'reject';

        // $model->load(['ScreeningForm' => Yii::$app->request->post()]);
        if ($model->rejectScreening()) {
            return [
                'success' => true,
                'message' => 'Screening rejected successfully',
                'data' => [
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    public function actionFinalSubmit($id)
    {
        $model = ScreeningForm::findSubmitterScreening(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
        }

        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        $model->scenario = 'final-submit';

        // $model->load(['ScreeningForm' => Yii::$app->request->post()]);
        if ($model->finalSubmitScreening()) {
            return [
                'success' => true,
                'message' => 'Screening finally submitted successfully',
                'data' => [
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }


    // todo Screening acceptance (final scheduling): In DECISION (respective program’s state), mark
// approved & finally submitted screening as SCHEDULED (final). Function can only be accessed by
// a PROGRAMMER.


}
