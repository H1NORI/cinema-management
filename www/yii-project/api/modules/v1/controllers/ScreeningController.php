<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\ProgramUserRoleForm;
use api\modules\v1\models\ScreeningForm;
use api\modules\v1\models\ScreeningUserRoleForm;
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
                // 'index',
                // 'search',
                // 'view',
            ],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        $screenings = ScreeningForm::findUserScreenings(Yii::$app->user->id);

        return [
            'success' => true,
            'message' => 'Screenings retrived',
            'data' => [
                'screenings' => $screenings,
            ],
        ];
    }

    public function actionSearch()
    {
        $model = new ScreeningForm();
        $model->scenario = 'search';

        $screenings = $model->search(['ScreeningForm' => $this->request->queryParams]);

        return [
            'success' => true,
            'message' => 'Screenings retrived',
            'data' => [
                'screenings' => $screenings,
            ],
        ];
    }

    // public function actionView($id)
    // {
    //     $model = ScreeningForm::findOne($id);

    //     if ($model == null) {
    //         throw new ApiException('PROGRAM_DOESNT_EXIST');
    //     }

    //     $userRole = Yii::$app->user->id ? ScreeningUserRoleForm::findScreeningUserRole(Yii::$app->user->id, $id)->role : null;

    //     return [
    //         'success' => true,
    //         'message' => 'Screening retrived',
    //         'data' => [
    //             'screening' => $model->toPublicArray($userRole),
    //         ],
    //     ];
    // }

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

    //TODO just started working on controller 
    public function actionUpdate($id)
    {
        $model = ScreeningForm::findUserScreening(Yii::$app->user->id, $id);

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
        $model = ScreeningForm::findUserScreening(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('SCREENING_DOESNT_EXIST');
        }

        // $model->scenario = 'submit';

        $model->load(['ScreeningForm' => Yii::$app->request->post()]);
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
        $model = ScreeningForm::findUserScreening(Yii::$app->user->id, $id);

        if ($model == null) {
            throw new ApiException('SCREENING_DOESNT_EXIST');
        }

        $model->scenario = 'withdraw';

        $model->load(['ScreeningForm' => Yii::$app->request->post()]);
        if ($model->withdrawScreening()) {
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

    public function actionAsignHandler($id)
    {
        $model = ScreeningForm::findUserScreening(Yii::$app->user->id, $id);

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

        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        $model->scenario = 'review';

        $model->load(['ScreeningForm' => Yii::$app->request->post()]);
        if ($model->reviewScreening()) {
            return [
                'success' => true,
                'message' => 'Screening review added successfully',
                'data' => [
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

    public function actionReject($id)
    {
        $model = ScreeningForm::findUserScreening(Yii::$app->user->id, $id);

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
        $model = ScreeningForm::findUserScreening(Yii::$app->user->id, $id);

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
