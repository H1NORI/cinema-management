<?php

namespace api\modules\v1\models;

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

    public function signin()
    {
        if (!$this->validate()) {
            return false;
        }

        if ($this->_user === null) {
            $this->addError('email', 1003);

            return false;
        }

        return $this->getAuthData(true);
    }

    private function getAuthData($sendCode = false)
    {
        $this->token = $this->generateJwt($this->_user);
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

    private function generateJwt($user)
    {
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();

        $now = new DateTimeImmutable();

        $jwtParams = Yii::$app->params['jwt'];

        $token = $jwt->getBuilder()
            // Configures the issuer (iss claim)
            ->issuedBy($jwtParams['issuer'])
            // Configures the audience (aud claim)
            ->permittedFor($jwtParams['audience'])
            // Configures the id (jti claim)
            ->identifiedBy($jwtParams['id'], true)
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($now)
            // Configures the time that the token can be used (nbf claim)
            ->canOnlyBeUsedAfter($now->modify($jwtParams['request_time']))
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($now->modify($jwtParams['expire']))
            // Configures a new claim, called "uid"
            ->withClaim('uid', $user->id)
            // Builds a new token
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
                $this->addError($attribute, 1003);
            } elseif (!$this->_user->validatePassword($this->password)) {
                $this->addError($attribute, 1004);
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


    public static function testGenerateJwt($userId)
    {
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();

        $now = new DateTimeImmutable();

        $jwtParams = Yii::$app->params['jwt'];

        $token = $jwt->getBuilder()
            // Configures the issuer (iss claim)
            ->issuedBy($jwtParams['issuer'])
            // Configures the audience (aud claim)
            ->permittedFor($jwtParams['audience'])
            // Configures the id (jti claim)
            ->identifiedBy($jwtParams['id'], true)
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($now)
            // Configures the time that the token can be used (nbf claim)
            // ->canOnlyBeUsedAfter($now->modify($jwtParams['request_time']))
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($now->modify($jwtParams['expire']))
            // Configures a new claim, called "uid"
            ->withClaim('uid', $userId)
            // Builds a new token
            ->getToken($signer, $key);

        return $token->toString();
    }


}