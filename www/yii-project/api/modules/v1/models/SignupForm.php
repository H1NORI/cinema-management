<?php 

namespace api\modules\v1\models;

use yii\base\Model;
use common\models\User;
use common\models\UserToken;

class SignupForm extends Model {
    public $username;
    public $email;
    public $password;
    private $_user;

    public function rules() {
        return [
            [['username', 'email', 'password'], 'trim'],
            ['username', 'required', 'on' => ['signup'], 'message' => 'USERNAME_REQUIRED'],
            ['username', 'validateUsername', 'on' => ['signup']],
            ['email', 'required', 'on' => ['signup'], 'message' => 'EMAIL_REQUIRED'],
            ['email', 'email', 'message' => 'INVALID_EMAIL_FORMAT'],
            ['email', 'validateEmail', 'on' => ['signup']],
            ['password', 'required', 'on' => ['signup'], 'message' => 'PASSWORD_REQUIRED'],
        ];
    }

    public function signup() { 
        if (!$this->validate()) {
            return false;
        }

        $user = new User();

        $user->username = $this->username;
        $user->email = $this->email;
        $user->status = User::STATUS_ACTIVE;

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

    public function validateUsername($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUserByUsername();

            if ($user) {
                $this->addError($attribute, 1009);
            }
        }
    }

    public function validateEmail($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUserByEmail();

            if ($user) {
                $this->addError($attribute, 1007);
            }
        }
    }

    protected function getUserByUsername() {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    protected function getUserByEmail() {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }

}