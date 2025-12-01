<?php

namespace api\modules\v1\models;

use common\exceptions\ApiException;
use common\models\ProgramUserRole;

class ProgramUserRoleForm extends ProgramUserRole
{

    public function rules()
    {
        return [

            ['role', 'required', 'message' => 'ROLE_REQUIRED'],
            [['role'], 'string', 'message' => 'INVALID_ROLE_TYPE'],
            ['role', 'in', 'range' => array_keys(self::optsRole()), 'message' => 'INVALID_ROLE'],


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

    public static function existProgramUserRole(int $userId, int $programId)
    {
        return self::find()->where(['user_id' => $userId, 'program_id' => $programId])->exists();
    }

    public static function existProgramUserRoleProgrammer(int $userId, int $programId)
    {
        return self::find()->where(['user_id' => $userId, 'program_id' => $programId, 'role' => self::ROLE_PROGRAMMER])->exists();
    }

    public static function existProgramUserRoleStaff(int $userId, int $programId)
    {
        return self::find()->where(['user_id' => $userId, 'program_id' => $programId, 'role' => self::ROLE_STAFF])->exists();
    }

    public static function findProgramUserRole(int $userId, int $programId)
    {
        return self::find()->where(['user_id' => $userId, 'program_id' => $programId])->one();
    }

    public static function addRole(int $userId, int $programId, string $role = self::ROLE_PROGRAMMER)
    {
        if (self::existProgramUserRole($userId, $programId)) {
            throw new ApiException('PROGRAM_USER_ROLE_EXIST');
        }

        $model = new ProgramUserRole();
        $model->program_id = $programId;
        $model->user_id = $userId;
        $model->role = $role;

        if (!$model->validate()) {
            throw ApiException::fromModel($model);
        }

        if (!$model->save(runValidation: false)) {
            throw new ApiException('ERROR_SAVING_PROGRAM_USER_ROLE');
        }

        return true;
    }

    public function deleteRole()
    {
        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if (!$this->delete()) {
            throw new ApiException('ERROR_DELETING_PROGRAM');
        }

        return true;
    }

}
