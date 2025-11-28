<?php

namespace console\controllers;

use yii\console\Controller;
use common\models\User;

class UserController extends Controller
{
    /**
     * Create a user
     *
     * @param string $email
     * @param string $username
     * @param string $password
     * @param string $role USER|ADMIN
     */
    public function actionCreate($email, $username, $password, $role = 'USER')
    {
        if (!in_array($role, ['USER', 'ADMIN'])) {
            echo "Invalid role (must be USER or ADMIN)\n";
            return 1;
        }

        $user = new User();
        $user->email = $email;
        $user->username = $username;
        $user->password_hash = \Yii::$app->security->generatePasswordHash($password);
        $user->auth_key = \Yii::$app->security->generateRandomString();
        $user->role = $role;
        $user->status = $user::STATUS_ACTIVE;

        if ($user->save()) {
            echo "User created with ID: {$user->id}\n";
        } else {
            print_r($user->errors);
        }
    }
}
