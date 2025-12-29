<?php

namespace api\modules\v1\models;

use common\exceptions\ApiException;
use common\models\Program;
use common\models\ProgramUserRole;
use common\models\User;
use Throwable;
use Yii;

class ProgramForm extends Program
{

    public $user_id;
    public $film_title;
    public $auditorium;

    public function rules()
    {
        return [
            ['user_id', 'required', 'on' => ['add-programmer', 'add-staff', 'remove-programmer', 'remove-staff'], 'message' => 'USER_ID_REQUIRED'],

            [['film_title', 'auditorium'], 'safe'],

            ['created_by', 'required', 'message' => 'USER_ID_REQUIRED'],
            // ['created_by', 'required', 'on' => ['create', 'update', 'toggle'], 'message' => 'USER_ID_REQUIRED'],

            ['name', 'required', 'on' => ['create', 'update'], 'message' => 'NAME_REQUIRED'],
            ['name', 'string', 'max' => 255, 'tooLong' => 'INVALID_NAME_TOO_LONG', 'message' => 'INVALID_NAME'],
            [['name'], 'unique', 'message' => 'NAME_TAKEN'],


            ['description', 'default', 'value' => ''],
            ['description', 'string', 'message' => 'INVALID_DESCRIPTION'],

            ['start_date', 'required', 'on' => ['create', 'update'], 'message' => 'START_DATE_REQUIRED'],
            ['start_date', 'date', 'format' => 'php:Y-m-d', 'message' => 'INVALID_START_DATE'],

            ['end_date', 'required', 'on' => ['create', 'update'], 'message' => 'END_DATE_REQUIRED'],
            ['end_date', 'date', 'format' => 'php:Y-m-d', 'message' => 'INVALID_END_DATE'],

            [['start_date', 'end_date'], 'validateDateRange', 'on' => ['create', 'update', 'search']],

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

        $scenarios['search'] = ['name', 'description', 'start_date', 'end_date', 'film_title', 'auditorium'];

        $scenarios['create'] = ['name', 'description', 'start_date', 'end_date'];
        $scenarios['update'] = ['name', 'description', 'start_date', 'end_date'];
        $scenarios['add-programmer'] = ['user_id'];
        $scenarios['add-staff'] = ['user_id'];
        $scenarios['remove-programmer'] = ['user_id'];
        $scenarios['remove-staff'] = ['user_id'];

        // $scenarios['update-state'] = ['user_id'];

        return $scenarios;
    }


    public static function findAnnouncedPrograms()
    {
        return self::find()->where(['state' => self::STATE_ANNOUNCED])->all();
    }

    public static function findUserPrograms(int $userId)
    {
        return self::find()->where(['created_by' => $userId])->all();
    }

    public static function findUserProgram(int $userId, int $id)
    {
        return self::findOne(['id' => $id, 'created_by' => $userId]);
    }

    public static function findProgram(int $id)
    {
        return self::findOne(['id' => $id]);
    }

    public function createProgram(int $userId)
    {
        $this->created_by = $userId;
        $this->state = Program::STATE_CREATED;

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!$this->save(false)) {
                throw new ApiException('ERROR_SAVING_PROGRAM');
            }

            ProgramUserRoleForm::addRole($userId, $this->id);
            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function updateProgram()
    {
        if ($this->isStateAnnounced()) {
            throw new ApiException('CANT_CHANGE_WHEN_ANNOUNCED');
        }

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_PROGRAM');
        }

        return true;
    }

    public function updateState()
    {
        if (!$this->canTransitionToNextState()) {
            throw new ApiException('INVALID_STATE_TRANSITION');
        }

        $this->state += 1;

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if ($this->isStateDecision()) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                if (!$this->save(false)) {
                    throw new ApiException('ERROR_SAVING_PROGRAM');
                }

                ScreeningForm::autoRejectScreenings($this->id);
                $transaction->commit();
                return true;
            } catch (Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
        }

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_PROGRAM');
        }

        return true;
    }

    public function addProgrammer()
    {
        if ($this->isStateAnnounced()) {
            throw new ApiException('CANT_CHANGE_WHEN_ANNOUNCED');
        }

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        $user = User::findIdentity($this->user_id);

        if (!$user) {
            throw new ApiException('USER_DOESNT_EXIST');
        }

        if ($user->isRoleAdmin()) {
            throw new ApiException('ADMIN_CANT_MANAGE_PROGRAM');
        }

        ProgramUserRoleForm::addRole($this->user_id, $this->id);

        return true;
    }

    public function removeProgrammer()
    {
        if ($this->isStateAnnounced()) {
            throw new ApiException('CANT_CHANGE_WHEN_ANNOUNCED');
        }

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if ($this->user_id == $this->created_by) {
            throw new ApiException('CREATOR_CANT_BE_REMOVED');
        }

        $role = ProgramUserRoleForm::findProgramUserRole($this->user_id, $this->id);

        if (!$role) {
            throw new ApiException('ERROR_DELETING_PROGRAM_ROLE');
        }

        $role->deleteRole();

        return true;
    }

    public function addStaff()
    {
        if ($this->state >= Program::STATE_SUBMISSION) {
            throw new ApiException('CANT_CHANGE_WHEN_SUBMISSION');
        }

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        $user = User::findIdentity($this->user_id);

        if (!$user) {
            throw new ApiException('USER_DOESNT_EXIST');
        }

        if ($user->isRoleAdmin()) {
            throw new ApiException('ADMIN_CANT_MANAGE_PROGRAM');
        }

        ProgramUserRoleForm::addRole($this->user_id, $this->id, ProgramUserRole::ROLE_STAFF);

        return true;
    }

    public function removeStaff()
    {
        if ($this->state >= Program::STATE_SUBMISSION) {
            throw new ApiException('CANT_CHANGE_WHEN_SUBMISSION');
        }

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        $role = ProgramUserRoleForm::findProgramUserRoleStaff($this->user_id, $this->id);

        if (!$role) {
            throw new ApiException('ERROR_DELETING_PROGRAM_ROLE');
        }

        $role->deleteRole();

        return true;
    }


    public function search($params, $formName = null)
    {
        $query = self::find()
            ->joinWith('screenings');

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

        // AND semantics автоматически обеспечивается andFilterWhere
        $query
            ->andFilterWhere(['like', 'programs.name', $this->name])
            ->andFilterWhere(['like', 'programs.description', $this->description])
            ->andFilterWhere(['like', 'screenings.state', ScreeningForm::STATE_SCHEDULED])
            ->andFilterWhere(['like', 'screenings.film_title', $this->film_title])
            ->andFilterWhere(['like', 'screenings.auditorium', $this->auditorium]);

        if ($this->start_date) {
            $query->andWhere(['>=', 'start_date', $this->start_date]);
        }

        if ($this->end_date) {
            $query->andWhere(['<=', 'end_date', $this->end_date]);
        }

        $query->orderBy([
            'programs.start_date' => SORT_ASC,
            'programs.name' => SORT_ASC,
        ]);

        return $query->all();
    }


    public function toPublicArray($role)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];

        if ($role == ProgramUserRole::ROLE_PROGRAMMER || $role == ProgramUserRole::ROLE_STAFF) {
            $data['state'] = $this->displayState();
            $data['created_by'] = $this->created_by;
            $data['created_at'] = $this->created_at;
            $data['updated_at'] = $this->updated_at;
        }

        return $data;
    }

    public function toPublicArrayView($role)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];

        if ($role === null) {
            return $data;
        }

        if ($role === ProgramUserRole::ROLE_PROGRAMMER || $role === ProgramUserRole::ROLE_STAFF) {
            $data['state'] = $this->displayState();
            $data['created_by'] = $this->created_by;
            $data['created_at'] = $this->created_at;
            $data['updated_at'] = $this->updated_at;

            $data['members'] = $this->getProgramMembersPublic();
        }

        return $data;
    }

    protected function getProgramMembersPublic(): array
    {
        $getProgramUserRoles = $this->getProgramUserRoles();

        foreach ($this->programUserRoles as $programUserRole) {
            $members[] = [
                'id' => $programUserRole->user->id,
                'username' => $programUserRole->user->username,
                'email' => $programUserRole->user->email,
                'role' => $programUserRole->role,
            ];
        }

        return $members;
    }


    public function deleteProgram()
    {
        if (!$this->isStateCreated()) {
            throw new ApiException('CAN_DELETE_ONLY_WHEN_CREATED');
        }

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if (!$this->delete()) {
            throw new ApiException('ERROR_DELETING_PROGRAM');
        }

        return true;
    }


    public function canTransitionToNextState()
    {
        $current = $this->state;
        $next = $this->state + 1;

        return isset(self::$allowedStateTransitions[$current]) && in_array($next, self::$allowedStateTransitions[$current], true);
    }

    public function validateDateRange($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        if (strtotime($this->start_date) >= strtotime($this->end_date)) {
            throw new ApiException('INVALID_START_DATE_MUST_BE_BEFORE_END_DATE');
        }
    }

}
