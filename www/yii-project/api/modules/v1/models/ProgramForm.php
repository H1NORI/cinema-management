<?php

namespace api\modules\v1\models;

use common\exceptions\ApiException;
use common\models\Program;

class ProgramForm extends Program
{

    public function rules()
    {
        return [
            // ['user_id', 'required', 'on' => ['create', 'update', 'toggle'], 'message' => 'USER_ID_REQUIRED'],

            // ['name', 'required', 'on' => ['create', 'update', 'toggle'], 'message' => 'NAME_REQUIRED'],
            // ['name', 'string', 'max' => 255, 'tooLong' => 'INVALID_NAME_TOO_LONG', 'message' => 'INVALID_NAME'],

            // ['description', 'default', 'value' => null],
            // ['description', 'string', 'message' => 'INVALID_DESCRIPTION'],

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

        $scenarios['create'] = ['name', 'description', 'start_date', 'end_date'];
        // $scenarios['update'] = ['name', 'description', 'start_date', 'end_date'];

        return $scenarios;
    }



    // public static function findUserTasks(int $userId)
    // {
    //     return self::find()->where(['user_id' => $userId])->all();
    // }

    // public static function findUserTask(int $userId, int $id)
    // {
    //     return self::findOne(['id' => $id, 'user_id' => $userId]);
    // }

    public function createProgram(int $userId)
    {
        $this->user_id = $userId;

        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_PROGRAM');
        }

        return true;
    }

    // public function updateTask()
    // {
    //     if (!$this->validate()) {
    //         throw ApiException::fromModel($this);
    //     }

    //     $this->updated_at = time();

    //     if (!$this->save(false)) {
    //         throw new ApiException('ERROR_SAVING_TASK');
    //     }

    //     return true;
    // }

    // public function deleteTask()
    // {
    //     if (!$this->validate()) {
    //         throw ApiException::fromModel($this);
    //     }

    //     if (!$this->delete()) {
    //         throw new ApiException('ERROR_DELETING_TASK');
    //     }

    //     return true;
    // }

    // public function toggleTask()
    // {
    //     $this->status = $this->status === self::STATUS_ACTIVE ? self::STATUS_INACTIVE : self::STATUS_ACTIVE;

    //     return $this->updateTask();
    // }

}
