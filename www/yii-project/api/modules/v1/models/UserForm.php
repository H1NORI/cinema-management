<?php

namespace api\modules\v1\models;

use common\exceptions\ApiException;
use common\models\User;

class UserForm extends User
{
    // public $username;
    // public $email;
    // public $password;
    private $_user;

    public function rules()
    {
        return [

            [['username', 'email', 'password'], 'trim'],
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


            // ['password', 'required', 'on' => ['update'], 'message' => 'PASSWORD_REQUIRED'],
            // [
            //     'password',
            //     'match',
            //     'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
            //     'message' => 'INVALID_PASSWORD_PATTERN',
            //     'on' => ['update']
            // ],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        // $scenarios['search'] = ['name', 'description', 'start_date', 'end_date'];

        // $scenarios['create'] = ['name', 'description', 'start_date', 'end_date'];
        $scenarios['update'] = ['username', 'email'];
        // $scenarios['add-programmer'] = ['user_id'];
        // $scenarios['add-staff'] = ['user_id'];

        // $scenarios['update-state'] = ['user_id'];

        return $scenarios;
    }

    public function updateUser()
    {
        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if ($this->isAttributeChanged('username')) {
            $this->token_version++;
        }

        if (!$this->save(false)) {
            throw new ApiException('ERROR_SAVING_PROGRAM');
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