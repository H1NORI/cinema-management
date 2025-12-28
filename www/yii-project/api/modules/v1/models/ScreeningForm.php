<?php

namespace api\modules\v1\models;

use common\exceptions\ApiException;
use common\models\Program;
use common\models\Screening;
use common\models\ScreeningReview;
use common\models\User;
use Throwable;
use Yii;

class ScreeningForm extends Screening
{

    public $user_id;
    public $isTimetable;


    public function rules()
    {
        return [
            [['isTimetable'], 'safe'],

            // Program ID validation
            ['program_id', 'required', 'on' => ['create', 'update', 'submit'], 'message' => 'PROGRAM_ID_REQUIRED'],
            ['program_id', 'integer', 'message' => 'INVALID_PROGRAM_ID_TYPE'],

            // Handler ID validation (required for assign-handler scenario)
            ['handler_id', 'required', 'on' => ['assign-handler'], 'message' => 'HANDLER_ID_REQUIRED'],

            // Time validation with custom validator to check start < end and duration
            ['start_time', 'required', 'on' => ['submit'], 'message' => 'START_TIME_REQUIRED'],
            ['start_time', 'date', 'format' => 'php:H:i:s', 'message' => 'INVALID_START_TIME'],

            ['end_time', 'required', 'on' => ['submit'], 'message' => 'END_TIME_REQUIRED'],
            ['end_time', 'date', 'format' => 'php:H:i:s', 'message' => 'INVALID_END_TIME'],

            // Custom validator for time range and duration check
            [['start_time', 'end_time'], 'validateTimeRange', 'on' => ['create', 'update', 'submit']],

            ['film_title', 'required', 'on' => ['submit'], 'message' => 'FILM_TITLE_REQUIRED'],
            ['film_title', 'string', 'max' => 255, 'message' => 'INVALID_FILM_TITLE'],

            // Film optional fields (strings with max length)
            //TODO make them required????
            ['film_cast', 'string', 'message' => 'INVALID_FILM_CAST'],
            ['film_genres', 'string', 'max' => 255, 'message' => 'INVALID_FILM_GENRES'],

            ['auditorium', 'required', 'on' => ['submit'], 'message' => 'AUDITORIUM_REQUIRED'],
            ['auditorium', 'string', 'max' => 255, 'message' => 'INVALID_AUDITORIUM'],

            ['film_duration', 'required', 'on' => ['submit'], 'message' => 'FILM_DURATION_REQUIRED'],
            ['film_duration', 'integer', 'message' => 'INVALID_FILM_DURATION'],


            // Review scenario (score and comments)
            ['score', 'required', 'on' => ['review'], 'message' => 'SCORE_REQUIRED'],
            ['score', 'integer', 'min' => 0, 'max' => 100, 'message' => 'INVALID_SCORE_TYPE', 'tooSmall' => 'INVALID_SCORE_RANGE', 'tooBig' => 'INVALID_SCORE_RANGE', 'on' => ['review']],

            ['comments', 'required', 'on' => ['review'], 'message' => 'COMMENTS_REQUIRED'],
            ['comments', 'string', 'message' => 'INVALID_COMMENTS_TYPE'],

            // Reject scenario (rejection reason)
            ['rejection_reason', 'required', 'on' => ['reject'], 'message' => 'REJECTION_REASON_REQUIRED'],
            ['rejection_reason', 'string', 'message' => 'INVALID_REJECTION_REASON_TYPE'],
        ];
    }

    /**
     * Validates that start_time is before end_time and calculates duration
     * @param $attribute
     * @param $params
     */
    public function validateTimeRange($attribute, $params)
    {
        // Преобразуем H:i в секунды
        $startTime = strtotime($this->start_time);
        $endTime = strtotime($this->end_time);

        if ($startTime === false || $endTime === false) {
            throw new ApiException('INVALID_START_TIME_MUST_BE_BEFORE_END_TIME');
        }

        if ($startTime >= $endTime) {
            throw new ApiException('INVALID_START_TIME_MUST_BE_BEFORE_END_TIME');
        }

        // film_duration хранится в минутах
        if ($this->film_duration) {
            $durationSeconds = $this->film_duration * 60;
            if (($endTime - $startTime) < $durationSeconds) {
                throw new ApiException('END_TIME_MUST_BE_GREATER_OR_EQUAL_FILM_DURATION');
            }
        }
    }


    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['search'] = ['film_title', 'film_cast', 'film_genres', 'start_time', 'end_time', 'isTimetable'];

        $scenarios['create'] = ['program_id', 'film_title', 'film_cast', 'film_genres', 'film_duration', 'auditorium', 'start_time', 'end_time'];
        $scenarios['update'] = ['film_title', 'film_cast', 'film_genres', 'film_duration', 'auditorium', 'start_time', 'end_time'];

        // $scenarios['submit'] = [];

        $scenarios['assign-handler'] = ['handler_id'];

        $scenarios['review'] = ['score', 'comments'];

        $scenarios['reject'] = ['rejection_reason'];

        // $scenarios['final-submit'] = ['film_cast', 'film_genres'];

        return $scenarios;
    }

    public static function findScheduledScreenings()
    {
        return self::find()->where(['state' => self::STATE_SCHEDULED])->all();
    }


    public static function findUserScreenings(int $userId)
    {
        return self::find()->where(['submitter_id' => $userId])->all();
    }

    public static function findSubmitterScreening(int $submitterId, int $id)
    {
        return self::findOne(['id' => $id, 'submitter_id' => $submitterId]);
    }

    public static function findHandlerScreening(int $handlerId, int $id)
    {
        return self::findOne(['id' => $id, 'handler_id' => $handlerId]);
    }

    public static function findScreening(int $id)
    {
        return self::findOne(['id' => $id]);
    }

    public static function autoRejectScreenings(int $programId)
    {
        $screenings = Screening::find()
            ->where([
                'program_id' => $programId,
                'state' => Screening::STATE_APPROVED,
            ])
            ->all();

        foreach ($screenings as $screening) {
            $screening->state = Screening::STATE_REJECTED;
            $screening->rejection_reason = 'Final submission was not provided before decision phase';

            if (!$screening->save(false)) {
                throw new ApiException('ERROR_SAVING_SCREENING');
            }
        }
    }

    public function createScreening(int $userId)
    {
        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        // с логики выходит описанной в документации что только Staff может создать screening хотя там написано что любой User
        if (!ProgramUserRoleForm::existProgramUserRoleStaff($userId, $this->program_id)) {
            throw new ApiException('STAFF_ROLE_REQUIRED');
        }

        $program = ProgramForm::findOne($this->program_id);

        if (!$program || !($program->state === Program::STATE_SUBMISSION || $program->state === Program::STATE_CREATED)) {
            throw new ApiException('PROGRAM_NOT_IN_SUBMISSION_OR_CREATED');
        }

        //todo возможно добавить в программу только если CREATED или SUBMISSION

        $this->submitter_id = $userId;
        $this->setStateToCreated();

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_SCREENING');
        }

        return true;
    }


    public function updateScreening()
    {
        if (!$this->isStateCreated()) {
            throw new ApiException('CAN_CHANGE_ONLY_WHEN_CREATED');
        }

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_SCREENING');
        }

        return true;
    }

    public function submitScreening()
    {
        if (!$this->isStateCreated()) {
            throw new ApiException('CAN_CHANGE_ONLY_WHEN_CREATED');
        }

        $program = Program::findOne($this->program_id);

        if (!$program || $program->state !== Program::STATE_SUBMISSION) {
            throw new ApiException('PROGRAM_NOT_IN_SUBMISSION');
        }

        if (!$this->canTransitionToNextState()) {
            throw new ApiException('INVALID_STATE_TRANSITION');
        }

        $this->state += 1;

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_SCREENING');
        }

        return true;
    }

    public function withdrawScreening()
    {
        if (!$this->isStateCreated()) {
            throw new ApiException('CAN_CHANGE_ONLY_WHEN_CREATED');
        }

        $program = Program::findOne($this->program_id);

        if (!$program || !($program->state === Program::STATE_SUBMISSION || $program->state === Program::STATE_CREATED)) {
            throw new ApiException('PROGRAM_NOT_IN_SUBMISSION_OR_CREATED');
        }

        if (!$this->delete()) {
            throw new ApiException('ERROR_DELETING_SCREENING');
        }

        return true;
    }

    public function assignScreeningHandler()
    {
        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        $program = Program::findOne($this->program_id);

        if (!$program || $program->state !== Program::STATE_ASSIGNMENT) {
            throw new ApiException('PROGRAM_NOT_IN_ASSIGNMENT');
        }

        if (!$this->isStateSubmitted()) {
            throw new ApiException('CAN_CHANGE_ONLY_WHEN_SUBMITTED');
        }

        if (!ProgramUserRoleForm::existProgramUserRoleStaff($this->handler_id, $this->program_id)) {
            throw new ApiException('PROGRAM_USER_ROLE_DOESNT_EXIST');
        }

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_SCREENING');
        }

        return true;
    }

    public function reviewScreening()
    {
        if (!$this->isStateSubmitted()) {
            throw new ApiException('CAN_CHANGE_ONLY_WHEN_SUBMITTED');
        }

        $program = Program::findOne($this->program_id);

        if (!$program || $program->state !== Program::STATE_REVIEW) {
            throw new ApiException('PROGRAM_NOT_IN_REVIEW');
        }

        if (!ProgramUserRoleForm::existProgramUserRoleStaff($this->handler_id, $this->program_id)) {
            throw new ApiException('STAFF_ROLE_REQUIRED');
        }

        if (!$this->canTransitionToNextState()) {
            throw new ApiException('INVALID_STATE_TRANSITION');
        }

        $this->state += 1;

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_SCREENING');
        }

        return true;
    }

    public function approveScreening()
    {
        if (!$this->isStateReviewed()) {
            throw new ApiException('CAN_CHANGE_ONLY_WHEN_REVIEWED');
        }

        $program = Program::findOne($this->program_id);

        // if (!$program || $program->state !== Program::STATE_SCHEDULING) {
        if (!$program || !($program->state === Program::STATE_SCHEDULING || $program->state === Program::STATE_DECISION)) {
            throw new ApiException('PROGRAM_NOT_IN_SCHEDULING_OR_DECISION');
        }

        if (!$this->canTransitionToNextState()) {
            throw new ApiException('INVALID_STATE_TRANSITION');
        }

        $this->state += 1;

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_SCREENING');
        }

        return true;
    }

    //TODO add Automatic by system: In DECISION, any approved but not finally submitted screening is auto-rejected.  
    public function rejectScreening()
    {
        if (!($this->isStateReviewed() || $this->isStateFinallySubmitted())) {
            throw new ApiException('CAN_CHANGE_ONLY_WHEN_REVIEWED_OR_FINALLY_SUBMITTED');
        }

        $program = Program::findOne($this->program_id);

        if (!$program || !($program->state === Program::STATE_SCHEDULING || $program->state === Program::STATE_DECISION)) {
            throw new ApiException('PROGRAM_NOT_IN_SCHEDULING_OR_DECISION');
        }

        $this->state = self::STATE_REJECTED;

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_SCREENING');
        }

        return true;
    }

    public function finalSubmitScreening()
    {
        if (!$this->isStateApproved()) {
            throw new ApiException('CAN_CHANGE_ONLY_WHEN_APPROVED');
        }

        $program = Program::findOne($this->program_id);

        if (!$program || $program->state !== Program::STATE_FINAL_PUBLICATION) {
            throw new ApiException('PROGRAM_NOT_IN_FINAL_PUBLICATION');
        }

        if (!$this->canTransitionToNextState()) {
            throw new ApiException('INVALID_STATE_TRANSITION');
        }

        $this->state += 1;

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_SCREENING');
        }

        return true;
    }

    public function acceptScreening()
    {
        if (!$this->isStateFinallySubmitted()) {
            throw new ApiException('CAN_CHANGE_ONLY_WHEN_FINALLY_SUBMITTED');
        }

        $program = Program::findOne($this->program_id);

        if (!$program || $program->state !== Program::STATE_DECISION) {
            throw new ApiException('PROGRAM_NOT_IN_DECISION');
        }

        if (!$this->canTransitionToNextState()) {
            throw new ApiException('INVALID_STATE_TRANSITION');
        }

        $this->state += 1;

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_SCREENING');
        }

        return true;
    }

    public function search($params, $formName = null)
    {
        $query = self::find();

        $this->load($params, $formName);

        // $this->pageSize = (int) ($this->pageSize ?: 20);

        // add conditions that should always apply here
        // $dataProvider = new ActiveDataProvider([
        //     'query' => $query,
        //     'pagination' => [
        //         'pageSize' => $this->pageSize,
        //     ],
        // ]);

        if (!$this->validate()) {
            return [];
        }

        if ($this->film_title) {
            $this->applyWordFilter($query, 'film_title', $this->film_title);
        }
        if ($this->film_cast) {
            $this->applyWordFilter($query, 'film_cast', $this->film_cast);
        }
        if ($this->film_genres) {
            $this->applyWordFilter($query, 'film_genres', $this->film_genres);
        }

        if ($this->start_time) {
            $query->andWhere(['>=', 'start_time', $this->start_time]);
        }

        if ($this->end_time) {
            $query->andWhere(['<=', 'end_time', $this->end_time]);
        }

        if ($this->isTimetable) {
            $query->orderBy(['start_time' => SORT_ASC]);
        } else {
            $query->orderBy([
                'film_genres' => SORT_ASC,
                'film_title' => SORT_ASC,
            ]);
        }

        return $query->all();
    }

    private function applyWordFilter($query, $column, $value)
    {
        $words = preg_split('/\s+/', trim($value));
        foreach ($words as $word) {
            $query->andWhere(['like', $column, $word]);
        }
    }


    public function toPublicArray($role)
    {
        $data = [
            'id' => $this->id,
            'film_title' => $this->film_title,
            'film_genres' => $this->film_genres,
            'film_cast' => $this->film_cast,
            'film_duration' => $this->film_duration,
            'auditorium' => $this->auditorium,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ];

        if ($role == ProgramUserRoleForm::ROLE_PROGRAMMER || $role == ProgramUserRoleForm::ROLE_STAFF) {
            $data['state'] = $this->displayState();
            $data['rejection_reason'] = $this->rejection_reason;
            $data['submitter_id'] = $this->submitter_id;
            $data['handler_id'] = $this->handler_id;
            $data['created_at'] = $this->created_at;
            $data['updated_at'] = $this->updated_at;
        }

        return $data;
    }


    public function canTransitionToNextState()
    {
        $current = $this->state;
        $next = $this->state + 1;

        return isset(self::$allowedStateTransitions[$current]) && in_array($next, self::$allowedStateTransitions[$current], true);
    }

}
