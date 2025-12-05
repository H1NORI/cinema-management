<?php

namespace api\modules\v1\models;

use common\exceptions\ApiException;
use common\models\UserRefreshToken;
use DateTimeImmutable;
use \Yii;
use yii\base\Model;
use common\models\User;
use common\models\UserToken;

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
            $this->addError('email', 1003);

            return false;
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
        $this->refreshToken = $this->generateRefreshToken($this->_user);

        // UserToken::clearToken($this->getUser()->getId(), $userToken->token);

        // if ($sendCode) {
        //     $code = \Yii::$app->respStandarts->generateCode();

        //     if ($this->saveOTPCode($code)) {
        //         $this->sendEmail($code);
        //     }
        // }

        // $this->responseData = [
        //     "response" => [
        //         "status" => true
        //     ],
        //     "user_id" => (int) $this->_user->id,
        //     "email" => $this->_user->email,
        //     "tokenData" => [
        //         "token" => $tokenModel->token
        //     ]
        // ];

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

    private function generateRefreshToken($user, $impersonator = null)
    {
        $refreshToken = Yii::$app->security->generateRandomString(200);

        // TODO: Don't always regenerate - you could reuse existing one if user already has one with same IP and user agent
        $userRefreshToken = new UserRefreshToken([
            'urf_userID' => $user->id,
            'urf_token' => $refreshToken,
            'urf_ip' => Yii::$app->request->userIP,
            'urf_user_agent' => Yii::$app->request->userAgent,
            'urf_created' => gmdate('Y-m-d H:i:s'),
        ]);
        if (!$userRefreshToken->save()) {
            $this->addError('email', 1005);
            return false;
        }

        // Send the refresh-token to the user in a HttpOnly cookie that Javascript can never read and that's limited by path
        Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'refresh-token',
            'value' => $refreshToken,
            'httpOnly' => true,
            'sameSite' => 'none',
            'secure' => true,
            'path' => '/v1/auth/refresh-token',  //endpoint URI for renewing the JWT token using this refresh-token, or deleting refresh-token
        ]));

        return $userRefreshToken;
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_user = $this->getUser();

            if (!$this->_user) {
                throw new ApiException(errorKey: 'USER_DOESNT_EXIST');
            } elseif (!$this->_user->validatePassword($this->password)) {
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