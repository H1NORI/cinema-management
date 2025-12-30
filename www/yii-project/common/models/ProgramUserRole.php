<?php

namespace common\models;

use common\components\DataLogBehavior;
use Yii;

/**
 * This is the model class for table "program_user_roles".
 *
 * @property int $id
 * @property int $program_id
 * @property int $user_id
 * @property string $role
 *
 * @property Program $program
 * @property User $user
 */
class ProgramUserRole extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ROLE_PROGRAMMER = 'PROGRAMMER';
    const ROLE_STAFF = 'STAFF';
    const ROLE_SUBMITTER = 'SUBMITTER';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_user_roles';
    }

    public function behaviors()
    {
        return [
            DataLogBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_id', 'user_id', 'role'], 'required'],
            [['program_id', 'user_id'], 'integer'],
            [['role'], 'string'],
            ['role', 'in', 'range' => array_keys(self::optsRole())],
            [['program_id', 'user_id'], 'unique', 'targetAttribute' => ['program_id', 'user_id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_id' => 'Program ID',
            'user_id' => 'User ID',
            'role' => 'Role',
        ];
    }

    /**
     * Gets query for [[Program]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::class, ['id' => 'program_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    /**
     * column role ENUM value labels
     * @return string[]
     */
    public static function optsRole()
    {
        return [
            self::ROLE_PROGRAMMER => 'PROGRAMMER',
            self::ROLE_STAFF => 'STAFF',
            self::ROLE_SUBMITTER => 'SUBMITTER',
        ];
    }

    /**
     * @return string
     */
    public function displayRole()
    {
        return self::optsRole()[$this->role];
    }

    /**
     * @return bool
     */
    public function isRoleProgrammer()
    {
        return $this->role === self::ROLE_PROGRAMMER;
    }

    public function setRoleToProgrammer()
    {
        $this->role = self::ROLE_PROGRAMMER;
    }

    /**
     * @return bool
     */
    public function isRoleStaff()
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function setRoleToStaff()
    {
        $this->role = self::ROLE_STAFF;
    }

    /**
     * @return bool
     */
    public function isRoleSubmitter()
    {
        return $this->role === self::ROLE_SUBMITTER;
    }

    public function setRoleToSubmitter()
    {
        $this->role = self::ROLE_SUBMITTER;
    }
}
