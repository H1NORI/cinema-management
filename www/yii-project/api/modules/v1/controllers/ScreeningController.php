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

    public function actionIndex($program_id)
    {
        $screenings = ScreeningForm::find()->where(['program_id' => $program_id])->all();

        $userId = Yii::$app->user->id;
        $isGuest = Yii::$app->user->isGuest;

        $result = [];

        if (!$isGuest && ProgramUserRoleForm::existProgramUserRoleProgrammer($userId, $program_id)) {
            foreach ($screenings as $screening) {
                $result[] = $screening->toPublicArray(ProgramUserRoleForm::ROLE_PROGRAMMER);
            }
        } else {
            foreach ($screenings as $screening) {
                if (!$isGuest) {
                    if (ScreeningForm::existSubmitterScreening($userId, $screening->id) || ScreeningForm::existHandlerScreening($userId, $screening->id)) {
                        $result[] = $screening->toPublicArray(ProgramUserRoleForm::ROLE_STAFF);
                    }
                } else if ($screening->state === $screening::STATE_SCHEDULED) {
                    $result[] = $screening->toPublicArray(null);
                }
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

        if (!$isGuest && ProgramUserRoleForm::existProgramUserRoleProgrammer($userId, $model->program_id)) {
            foreach ($screenings as $screening) {
                $result[] = $screening->toPublicArray(ProgramUserRoleForm::ROLE_PROGRAMMER);
            }
        } else {
            foreach ($screenings as $screening) {
                if (!$isGuest) {
                    if (ScreeningForm::existSubmitterScreening($userId, $screening->id) || ScreeningForm::existHandlerScreening($userId, $screening->id)) {
                        $result[] = $screening->toPublicArray(ProgramUserRoleForm::ROLE_STAFF);
                    }
                } else if ($screening->state === $screening::STATE_SCHEDULED) {
                    $result[] = $screening->toPublicArray(null);
                }
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

        $userId = Yii::$app->user->id;
        $isGuest = Yii::$app->user->isGuest;
        $result = [];

        if (!$isGuest && ProgramUserRoleForm::existProgramUserRoleProgrammer($userId, $model->program_id)) {
            $result = $model->toPublicArrayView(ProgramUserRoleForm::ROLE_PROGRAMMER);
        } else {
            if (!$isGuest) {
                if (ScreeningForm::existSubmitterScreening($userId, $id) || ScreeningForm::existHandlerScreening($userId, $id)) {
                    $result = $model->toPublicArrayView(ProgramUserRoleForm::ROLE_STAFF);
                }
            } else if ($model->state === $model::STATE_SCHEDULED) {
                $result = $model->toPublicArrayView(null);
            }
        }

        $userRole = Yii::$app->user->id ? ProgramUserRoleForm::findProgramUserRole(Yii::$app->user->id, $model->program_id)?->role : null;

        return [
            'success' => true,
            'message' => 'Screening retrived',
            'data' => [
                'screening' => $result,
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
                'message' => 'Screening handler assigned successfully',
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

        // $model->scenario = 'approve';

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
        $model = ScreeningForm::findScreening($id);

        if ($model == null) {
            throw new ApiException('SCREENING_DOESNT_EXIST');
        }

        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $model->program_id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }

        $model->scenario = 'reject';

        $model->load(['ScreeningForm' => Yii::$app->request->post()]);
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

        // $model->scenario = 'final-submit';

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

    public function actionAccept($id)
    {
        $model = ScreeningForm::findScreening($id);

        if ($model == null) {
            throw new ApiException('SCREENING_DOESNT_EXIST');
        }

        if (!ProgramUserRoleForm::existProgramUserRoleProgrammer(Yii::$app->user->id, $model->program_id)) {
            throw new ApiException('PROGRAMER_ROLE_REQUIRED');
        }
        // $model->scenario = 'accept';

        // $model->load(['ScreeningForm' => Yii::$app->request->post()]);
        if ($model->acceptScreening()) {
            return [
                'success' => true,
                'message' => 'Screening finally accepted successfully',
                'data' => [
                ],
            ];
        }

        throw new ApiException('UNEXPECTED_ERROR');
    }

}
