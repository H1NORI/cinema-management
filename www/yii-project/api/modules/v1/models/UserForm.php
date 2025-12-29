<?php

namespace api\modules\v1\models;

use common\exceptions\ApiException;
use common\models\User;
use Yii;
use yii\web\UnauthorizedHttpException;

class UserForm extends User
{
    public $password;
    public $new_password;
    public $confirm_password;
    private $_user;

    public function rules()
    {
        return [

            [['username', 'email', 'password', 'new_password', 'confirm_password'], 'trim'],
            ['username', 'required', 'on' => ['update'], 'message' => 'USERNAME_REQUIRED'],
            ['username', 'validateUsername', 'on' => ['update']],
            [
                'username',
                'match',
                'pattern' => '/^[A-Za-z][A-Za-z0-9_]{4,}$/',
                'message' => 'INVALID_USERNAME_PATTERN',
                'on' => ['update']
            ],

            ['email', 'required', 'on' => ['update'], 'message' => 'EMAIL_REQUIRED'],
            ['email', 'email', 'message' => 'INVALID_EMAIL_FORMAT'],
            ['email', 'validateEmail', 'on' => ['update']],


            [['password', 'new_password', 'confirm_password'], 'required', 'on' => ['update-password'], 'message' => 'PASSWORD_REQUIRED'],
            [
                ['password', 'new_password', 'confirm_password'],
                'match',
                'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
                'message' => 'INVALID_PASSWORD_PATTERN',
                'on' => ['update-password']
            ],

            ['status', 'required', 'on' => ['update-status'], 'message' => 'STATUS_REQUIRED'],
            ['status', 'in', 'range' => [self::STATUS_DELETED, self::STATUS_INACTIVE, self::STATUS_ACTIVE], 'message' => 'INVALID_STATUS'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['update'] = ['username', 'email'];
        $scenarios['update-password'] = ['password', 'new_password', 'confirm_password'];
        $scenarios['update-status'] = ['status'];
        $scenarios['delete'] = [];

        return $scenarios;
    }

    public function updateUser()
    {
        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if ($this->isAttributeChanged('username')) {
            $this->access_token = null;
        }

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_USER');
        }

        return true;
    }

    public function updatePasswordUser()
    {
        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if (!$this->validatePassword($this->password)) {
            $this->incrementFailCount();
            if ($this->password_fail_count >= 3) {
                $this->deactivateAccountAndIncrementTokenVersion();
                throw new UnauthorizedHttpException();
            }
            throw new ApiException(errorKey: 'INVALID_PASSWORD');
        }

        if ($this->new_password !== $this->confirm_password) {
            $this->incrementFailCount();
            if ($this->password_fail_count >= 3) {
                $this->deactivateAccountAndIncrementTokenVersion();
                throw new UnauthorizedHttpException('The user is inactive.');
            }
            throw new ApiException('PASSWORDS_SHOULD_BE_EQUAL');
        }

        $this->access_token = null;
        $this->password_fail_count = 0;

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_USER');
        }

        return true;
    }

    public function updateStatusUser()
    {
        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        $this->access_token = null;

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_USER');
        }

        return true;
    }

    public function deleteUser()
    {
        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if (!$this->delete()) {
            throw new ApiException('ERROR_DELETING_USER');
        }

        return true;
    }

    public function validateUsername($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserByUsername();

            if ($user && $this->id !== $user->id) {
                throw new ApiException(errorKey: 'USERNAME_TAKEN');
            }
        }
    }

    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserByEmail();

            if ($user && $this->id !== $user->id) {
                throw new ApiException(errorKey: 'EMAIL_TAKEN');
            }
        }
    }

    protected function getUserByUsername()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    protected function getUserByEmail()
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }

}