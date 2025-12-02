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
    public $score;
    public $comments;


    public function rules()
    {
        return [

            // [['film_title', 'film_cast', 'film_genres', 'film_duration', 'auditorium', 'start_time', 'end_time', 'handler_id'], 'default', 'value' => null],
            // [['state'], 'default', 'value' => 'CREATED'],
            // [['program_id', 'submitter_id', 'created_at', 'updated_at'], 'required'],
            // [['program_id', 'film_duration', 'submitter_id', 'handler_id', 'created_at', 'updated_at'], 'integer'],
            // [['state', 'film_cast'], 'string'],
            // [['start_time', 'end_time'], 'safe'],
            // [['film_title', 'film_genres', 'auditorium'], 'string', 'max' => 255],
            // ['state', 'in', 'range' => array_keys(self::optsState())],
            // [['handler_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['handler_id' => 'id']],
            // [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_id' => 'id']],
            // [['submitter_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['submitter_id' => 'id']],


            // ['user_id', 'required', 'on' => ['add-screeningmer', 'add-staff'], 'message' => 'USER_ID_REQUIRED'],

            // ['created_by', 'required', 'message' => 'USER_ID_REQUIRED'],
            // ['created_by', 'required', 'on' => ['create', 'update', 'toggle'], 'message' => 'USER_ID_REQUIRED'],

            ['name', 'required', 'on' => ['create', 'update'], 'message' => 'NAME_REQUIRED'],
            ['name', 'string', 'max' => 255, 'tooLong' => 'INVALID_NAME_TOO_LONG', 'message' => 'INVALID_NAME'],
            [['name'], 'unique', 'message' => 'NAME_TAKEN'],

            ['program_id', 'required', 'on' => ['create', 'update'], 'message' => 'PROGRAM_ID_REQUIRED'],
            ['program_id', 'integer', 'message' => 'INVALID_PROGRAM_ID_TYPE'],

            ['handler_id', 'required', 'on' => ['assign-handler',], 'message' => 'HANDLER_ID_REQUIRED'],

            // ['description', 'default', 'value' => null],
            // ['description', 'string', 'message' => 'INVALID_DESCRIPTION'],

            //todo check difference beetwen time start and end, also check duration of film 
            ['start_time', 'required', 'on' => ['create', 'update'], 'message' => 'START_DATE_REQUIRED'],
            ['start_time', 'date', 'format' => 'php:Y-m-d H:i:s', 'message' => 'INVALID_START_DATE'],

            ['end_time', 'required', 'on' => ['create', 'update'], 'message' => 'END_DATE_REQUIRED'],
            ['end_time', 'date', 'format' => 'php:Y-m-d H:i:s', 'message' => 'INVALID_END_DATE'],


            //TODO maybe I needd to add some scopes
            ['score', 'required', 'on' => ['review',], 'message' => 'SCORE_REQUIRED'],
            ['score', 'integer', 'message' => 'INVALID_SCORE_TYPE'],

            ['comments', 'required', 'on' => ['review',], 'message' => 'COMMENTS_REQUIRED'],
            ['comments', 'string', 'message' => 'INVALID_COMMENTS_TYPE'],

            ['rejection_reason', 'required', 'on' => ['reject',], 'message' => 'REJECTION_REASON_REQUIRED'],
            ['rejection_reason', 'string', 'message' => 'INVALID_REJECTION_REASON_TYPE'],

            // ['status', 'default', 'value' => self::STATUS_INACTIVE],
            // ['status', 'integer', 'message' => 'INVALID_STATUS_TYPE'],
            // ['status', 'required', 'on' => ['create', 'update', 'toggle'], 'message' => 'STATUS_REQUIRED'],
            // ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE], 'message' => 'INVALID_STATUS'],

            // ['priority', 'default', 'value' => self::PRIORITY_LOW],
            // ['priority', 'integer', 'message' => 'INVALID_PRIORITY_TYPE'],
            // ['priority', 'required', 'on' => ['create', 'update', 'toggle'], 'message' => 'PRIORITY_REQUIRED'],
            // ['priority', 'in', 'range' => [self::PRIORITY_LOW, self::PRIORITY_MEDIUM, self::PRIORITY_HIGH], 'message' => 'INVALID_PRIORITY'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        // $scenarios['search'] = ['name', 'description', 'start_date', 'end_date'];

        $scenarios['create'] = ['program_id', 'film_title', 'film_cast', 'film_genres', 'film_duration', 'auditorium', 'start_time', 'end_time'];
        $scenarios['update'] = ['film_title', 'film_cast', 'film_genres', 'film_duration', 'auditorium', 'start_time', 'end_time'];

        $scenarios['assign-handler'] = ['handler_id'];

        $scenarios['review'] = ['score', 'commnets'];

        // $scenarios['update-state'] = ['user_id'];

        return $scenarios;
    }



    public static function findUserScreenings(int $userId)
    {
        return self::find()->where(['submitter_id' => $userId])->all();
    }

    public static function findUserScreening(int $userId, int $id)
    {
        return self::findOne(['id' => $id, 'submitter_id' => $userId]);
    }

    public static function findHandlerScreening(int $userId, int $id)
    {
        return self::findOne(['id' => $id, 'handler_id' => $userId]);
    }

    public function createScreening(int $userId)
    {
        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        $program = ProgramForm::findOne($this->program_id);

        if ($program == null) {
            throw new ApiException('PROGRAM_DOESNT_EXIST');
        }

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
            throw new ApiException('CAN_CHABGE_ONLY_WHEN_CREATED');
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
            throw new ApiException('CAN_CHABGE_ONLY_WHEN_CREATED');
        }

        $program = Program::findOne($this->program_id);

        if (!$program || $program->state !== Program::STATE_SUBMISSION) {
            throw new ApiException('PROGRAM_NOT_IN_SUBMISSION');
        }

        // //todo use VALIDATE INSTEAD or add more conditions 
        // if (!trim($this->film_title) || !trim($this->auditorium) || !trim($this->end_time)) {
        //     throw new ApiException('SCREENING_INCOMPLETE');
        // }

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
            throw new ApiException('CAN_CHABGE_ONLY_WHEN_CREATED');
        }

        $program = Program::findOne($this->program_id);

        if (!$program || $program->state !== Program::STATE_SUBMISSION) {
            throw new ApiException('PROGRAM_NOT_IN_SUBMISSION');
        }

        if (!$this->delete()) {
            throw new ApiException('ERROR_DELETING_SCREENING');
        }

        return true;
    }

    public function assignScreeningHandler()
    {
        $program = Program::findOne($this->program_id);

        if (!$program || $program->state !== Program::STATE_ASSIGNMENT) {
            throw new ApiException('PROGRAM_NOT_IN_ASSIGNMENT');
        }

        if (!$this->isStateSubmitted()) {
            throw new ApiException('CAN_CHABGE_ONLY_WHEN_SUBMITTED');
        }

        if (!ProgramUserRoleForm::existProgramUserRoleStaff($this->handler_id, $this->program_id)) {
            throw new ApiException('PROGRAM_USER_ROLE_DOESNT_EXIST');
        }

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_SCREENING');
        }

        return true;
    }

    public function reviewScreening()
    {
        $program = Program::findOne($this->program_id);

        if (!$program || $program->state !== Program::STATE_REVIEW) {
            throw new ApiException('PROGRAM_NOT_IN_REVIEW');
        }

        if (!$this->isStateSubmitted()) {
            throw new ApiException('CAN_CHABGE_ONLY_WHEN_SUBMITTED');
        }

        if (!ProgramUserRoleForm::existProgramUserRoleStaff($this->handler_id, $this->program_id)) {
            throw new ApiException('PROGRAM_USER_ROLE_DOESNT_EXIST');
        }

        if (!$this->canTransitionToNextState()) {
            throw new ApiException('INVALID_STATE_TRANSITION');
        }

        $this->state += 1;

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!$this->save(false)) {
                throw new ApiException('ERROR_SAVING_SCREENING');
            }

            ScreeningReviewForm::addReview($this->handler_id, $this->id, $this->score, $this->comments);
            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    //TODO add Automatic by system: In DECISION, any approved but not finally submitted screening is auto-rejected.  
    public function rejectScreening()
    {
        $program = Program::findOne($this->program_id);

        if (!$program || !($program->state === Program::STATE_SCHEDULING || $program->state === Program::STATE_DECISION)) {
            throw new ApiException('PROGRAM_NOT_IN_SCHEDULING_OR_DECISION');
        }

        //todo not sure about this IF STATEMENT
        if (!$this->isStateReviewed()) {
            throw new ApiException('CAN_CHABGE_ONLY_WHEN_REVIWED');
        }

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
            throw new ApiException('CAN_CHABGE_ONLY_WHEN_APPROVED');
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

        // TODO Screening search: It shall be possible to search screenings by name, description, dates, film title,
        // auditorium with AND semantics (when the previously mentioned parameters have values
        // supplied to them, then they are AND combined meaning that all these conditions must be
        // satisfied in order to return the respective screening.). If no criteria are supplied, then all
        // screenings must be returned. In any case, the results must be filtered by role and then sorted
        // first by date and then by name.

        $query->andFilterWhere(['like', 'film_title', $this->film_title])
            ->andFilterWhere(['like', 'film_cast', $this->film_cast])
            ->andFilterWhere(['like', 'film_genres', $this->film_genres]);

        if ($this->start_time) {
            $query->andWhere(['>=', 'start_time', $this->start_time]);
        }

        if ($this->end_time) {
            $query->andWhere(['<=', 'end_time', $this->end_time]);
        }

        $query->orderBy(['film_genres' => SORT_ASC,  'film_title' => SORT_ASC, 'end_time' => SORT_ASC,]);

        return $query->all();
    }


    // public function toPublicArray($role)
    // {
    //     $data = [
    //         'id' => $this->id,
    //         'name' => $this->name,
    //         'description' => $this->description,
    //         'start_date' => $this->start_date,
    //         'end_date' => $this->end_date,
    //     ];

    //     if ($role == ScreeningUserRole::ROLE_PROGRAMMER || $role == ScreeningUserRole::ROLE_STAFF) {
    //         $data['state'] = $this->displayState();
    //         $data['created_at'] = $this->created_at;
    //         $data['updated_at'] = $this->updated_at;
    //     }

    //     return $data;
    // }


    public function canTransitionToNextState()
    {
        $current = $this->state;
        $next = $this->state + 1;

        return isset(self::$allowedStateTransitions[$current]) && in_array($next, self::$allowedStateTransitions[$current], true);
    }

}
