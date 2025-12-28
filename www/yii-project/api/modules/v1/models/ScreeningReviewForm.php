<?php

namespace api\modules\v1\models;

use common\exceptions\ApiException;
use common\models\ProgramUserRole;
use common\models\ScreeningReview;

class ScreeningReviewForm extends ScreeningReview
{

    public function rules()
    {
        return [

            // ['role', 'required', 'message' => 'ROLE_REQUIRED'],
            // [['role'], 'string', 'message' => 'INVALID_ROLE_TYPE'],
            // ['role', 'in', 'range' => array_keys(self::optsRole()), 'message' => 'INVALID_ROLE'],


            // ['created_by', 'required', 'on' => ['create', 'update', 'toggle'], 'message' => 'USER_ID_REQUIRED'],

            // ['name', 'required', 'message' => 'NAME_REQUIRED'],
            // ['name', 'string', 'max' => 255, 'tooLong' => 'INVALID_NAME_TOO_LONG', 'message' => 'INVALID_NAME'],
            // [['name'], 'unique', 'message' => 'NAME_TAKEN'],


            // ['description', 'default', 'value' => null],
            // ['description', 'string', 'message' => 'INVALID_DESCRIPTION'],

            // ['start_date', 'required', 'message' => 'START_DATE_REQUIRED'],
            // ['start_date', 'date', 'format' => 'php:Y-m-d', 'message' => 'INVALID_START_DATE'],

            // ['end_date', 'required', 'message' => 'END_DATE_REQUIRED'],
            // ['end_date', 'date', 'format' => 'php:Y-m-d', 'message' => 'INVALID_END_DATE'],

        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        // $scenarios['create'] = ['name', 'description', 'start_date', 'end_date'];
        // $scenarios['update'] = ['name', 'description', 'start_date', 'end_date'];

        return $scenarios;
    }

    public static function existScreeningReview(int $reviewerId, int $screeningId)
    {
        return self::find()->where(['reviewer_id' => $reviewerId, 'screening_id' => $screeningId])->exists();
    }

    public static function addReview(int $reviewerId, int $screeningId, int $score, string $comments)
    {
        $model = new ScreeningReview;
        $model->reviewer_id = $reviewerId;
        $model->screening_id = $screeningId;
        $model->score = $score;
        $model->comments = $comments;
        $model->created_at = time();

        if (!$model->validate()) {
            throw new ApiException('ERROR_SAVING_SCREENING_REVIEW');
            // throw ApiException::fromModel($model);
        }

        if (!$model->save(runValidation: false)) {
            throw new ApiException('ERROR_SAVING_SCREENING_REVIEW');
        }

        return true;
    }

}
