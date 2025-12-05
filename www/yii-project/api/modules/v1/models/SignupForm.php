<?php

namespace api\modules\v1\models;

use common\exceptions\ApiException;
use yii\base\Model;
use common\models\User;
use common\models\UserToken;

class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    private $_user;

    public function rules()
    {
        return [
            [['username', 'email', 'password'], 'trim'],
            ['username', 'required', 'on' => ['signup'], 'message' => 'USERNAME_REQUIRED'],
            ['username', 'validateUsername', 'on' => ['signup']],
            [
                'username',
                'match',
                'pattern' => '/^[A-Za-z][A-Za-z0-9_]{4,}$/',
                'message' => 'INVALID_USERNAME_PATTERN',
                'on' => ['signup']
            ],

            ['email', 'required', 'on' => ['signup'], 'message' => 'EMAIL_REQUIRED'],
            ['email', 'email', 'message' => 'INVALID_EMAIL_FORMAT'],
            ['email', 'validateEmail', 'on' => ['signup']],


            ['password', 'required', 'on' => ['signup'], 'message' => 'PASSWORD_REQUIRED'],
            [
                'password',
                'match',
                'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
                'message' => 'INVALID_PASSWORD_PATTERN',
                'on' => ['signup']
            ],
        ];
    }

    public function signup()
    {
        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        $user = new User();

        $user->username = $this->username;
        $user->email = $this->email;
        $user->status = User::STATUS_INACTIVE;

        $user->setPassword($this->password);
        $user->generateAuthKey();

        if ($user->save(false)) {
            // $code = $this->generateCode();

            // if ($this->saveCode($code)) {
            //     $this->sendEmail($user, $code);
            // }

            return true;
        }

        return false;
    }

    public function validateUsername($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserByUsername();

            if ($user) {
                throw new ApiException('USERNAME_TAKEN');
            }
        }
    }

    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserByEmail();

            if ($user) {
                throw new ApiException('EMAIL_TAKEN');
            }
        }
    }

    protected function getUserByUsername()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsernameAny($this->username);
        }

        return $this->_user;
    }

    protected function getUserByEmail()
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmailAny($this->email);
        }

        return $this->_user;
    }

}