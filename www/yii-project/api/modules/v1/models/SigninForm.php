<?php

namespace api\modules\v1\models;

use common\exceptions\ApiException;
use common\models\UserRefreshToken;
use DateTimeImmutable;
use \Yii;
use yii\base\Model;
use common\models\User;

class SigninForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $token;
    public $refreshToken;
    private $_user;

    public function rules()
    {
        return [
            [['username', 'email', 'password'], 'trim'],

            ['email', 'required', 'on' => ['signin'], 'message' => 'EMAIL_REQUIRED'],
            ['email', 'email', 'message' => 'INVALID_EMAIL_FORMAT'],

            ['password', 'required', 'on' => ['signin'], 'message' => 'PASSWORD_REQUIRED'],
            ['password', 'validatePassword'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['logout'] = [];

        return $scenarios;
    }

    public function signin()
    {
        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        if ($this->_user === null) {
            return false;
        }

        $this->_user->token_version += 1;
        $this->_user->password_fail_count = 0;

        if (!$this->_user->save(false, ['token_version', 'password_fail_count'])) {
            throw new ApiException('ERROR_SAVING_USER');
        }

        return $this->getAuthData(true);
    }

    public function logout($id)
    {
        if (!$this->validate()) {
            throw ApiException::fromModel($this);
        }

        $user = UserForm::findOne($id);

        if (!$user) {
            throw new ApiException('USER_DOESNT_EXIST');
        }

        $user->token_version += 1;

        if (!$user->save(false)) {
            throw new ApiException('ERROR_SAVING_USER');
        }

        return true;
    }

    private function getAuthData($sendCode = false)
    {
        $this->token = self::generateJwt($this->_user);

        return $this->token;
    }

    public static function generateJwt($user)
    {
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();

        $now = new DateTimeImmutable();

        $jwtParams = Yii::$app->params['jwt'];

        $token = $jwt->getBuilder()
            ->issuedBy($jwtParams['issuer'])
            ->permittedFor($jwtParams['audience'])
            ->identifiedBy($jwtParams['id'], true)
            ->issuedAt($now)
            // ->canOnlyBeUsedAfter($now->modify($jwtParams['request_time']))
            ->expiresAt($now->modify($jwtParams['expire']))
            ->withClaim('uid', $user->id)
            ->withClaim('tv', $user->token_version)
            ->getToken($signer, $key);

        return $token->toString();
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_user = $this->getUser();

            if (!$this->_user) {
                throw new ApiException(errorKey: 'USER_DOESNT_EXIST');
            } elseif (!$this->_user->validatePassword($this->password)) {
                $this->_user->incrementFailCount();
                if ($this->_user->password_fail_count >= 3) {
                    $this->_user->deactivateAccountAndIncrementTokenVersion();
                    throw new UnauthorizedHttpException();
                }
                throw new ApiException(errorKey: 'INVALID_PASSWORD');
            }
        }
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }

    public function getUserId()
    {
        return $this->_user?->id;
    }

    public function getUserEmail()
    {
        return $this->_user?->email;
    }

}