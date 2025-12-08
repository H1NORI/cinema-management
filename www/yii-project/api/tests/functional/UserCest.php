<?php

namespace api\tests\functional;

use api\modules\v1\models\SigninForm;
use api\tests\FunctionalTester;
use common\models\Program;
use common\models\User;
use Yii;


class UserCest
{

    private int $adminId = 999999;
    private int $programmerId = 999991;
    private int $staffId = 999992;

    private int $secondProgrammerId = 999993;


    private string $adminJwt;
    private string $programmerJwt;
    private string $staffJwt;
    private string $secondProgrammerJwt;

    private string $newAdminJwt;
    private string $newUserJwt;
    private string $newUserId;


    private static bool $initialized = false;

    public function _before(FunctionalTester $I)
    // public function _beforeSuite(FunctionalTester $I)
    {
        if (Yii::$app === null) {
            $config = require __DIR__ . '/../../config/test.php';
            new \yii\web\Application($config);
        }

        if (!self::$initialized) {
            $this->createTestUsers($I);
            self::$initialized = true;
        }
    }

    private function createTestUsers(FunctionalTester $I)
    {
        $I->comment("=== Creating test users... ===");

        $user = User::findOne($this->adminId) ?? new User();
        $user->id = $this->adminId;
        $user->email = 'test_admin@test.com';
        $user->username = 'Test_admin';
        $user->password_hash = Yii::$app->security->generatePasswordHash('Net12345_');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->role = 'ADMIN';
        $user->status = $user::STATUS_ACTIVE;
        $user->save();

        $this->adminJwt = SigninForm::generateJwt($user);

        $user = User::findOne($this->programmerId) ?? new User();
        $user->id = $this->programmerId;
        $user->email = 'test_programmer@test.com';
        $user->username = 'Test_programmer';
        $user->password_hash = Yii::$app->security->generatePasswordHash('Net12345_');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->role = 'USER';
        $user->status = $user::STATUS_ACTIVE;
        $user->save();

        $this->programmerJwt = SigninForm::generateJwt($user);

        $user = User::findOne($this->staffId) ?? new User();
        $user->id = $this->staffId;
        $user->email = 'test_staff@test.com';
        $user->username = 'Test_staff';
        $user->password_hash = Yii::$app->security->generatePasswordHash('Net12345_');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->role = 'USER';
        $user->status = $user::STATUS_ACTIVE;
        $user->save();

        $this->staffJwt = SigninForm::generateJwt($user);


        $user = User::findOne($this->secondProgrammerId) ?? new User();
        $user->id = $this->secondProgrammerId;
        $user->email = 'test_second_programmer@test.com';
        $user->username = 'Test_second_programmer';
        $user->password_hash = Yii::$app->security->generatePasswordHash('Net12345_');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->role = 'USER';
        $user->status = $user::STATUS_ACTIVE;
        $user->save();

        $this->secondProgrammerJwt = SigninForm::generateJwt($user);

    }

    public function deleteUsers(FunctionalTester $I)
    {
        User::findByEmailAny('test@gmail.com')->delete();
    }

    /* ----------------------------------------------------
     * SIGN UP
     * ---------------------------------------------------- */

    public function signupEmptyUsername(FunctionalTester $I)
    {
        $I->sendPOST('/auth/signup', []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1003,
            'message' => 'Username cannot be empty',
        ]);
    }

    public function signupInvalidUsernamePattern(FunctionalTester $I)
    {
        $I->sendPOST('/auth/signup', [
            'username' => 'test',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2015,
            'message' => 'Invalid username pattern! starts with a letter, ≥ 5 chars, allowed characters include alphanumeric and underscore',
        ]);
    }

    public function signupUsernameAlreadyTaken(FunctionalTester $I)
    {
        $I->sendPOST('/auth/signup', [
            'username' => 'Test_staff',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 3002,
            'message' => 'Username has already been taken',
        ]);
    }

    public function signupEmptyEmail(FunctionalTester $I)
    {
        $I->sendPOST('/auth/signup', [
            'username' => 'Test_user',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1001,
            'message' => 'Email cannot be empty',
        ]);
    }

    public function signupInvalidEmailPattern(FunctionalTester $I)
    {
        $I->sendPOST('/auth/signup', [
            'username' => 'Test_user',
            'email' => 'test@gmailcom'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2002,
            'message' => 'Invalid email format',
        ]);
    }

    public function signupEmptyPassword(FunctionalTester $I)
    {
        $I->sendPOST('/auth/signup', [
            'username' => 'Test_user',
            'email' => 'test@gmail.com'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1002,
            'message' => 'Password cannot be empty',
        ]);
    }

    public function signupInvalidPasswordPattern(FunctionalTester $I)
    {
        $I->sendPOST('/auth/signup', [
            'username' => 'Test_user',
            'email' => 'test@gmail.com',
            'password' => 'Net12345'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2016,
            'message' => 'Invalid password pattern! length ≥ 8 chars with upper and lower letters, digits and special characters',
        ]);
    }

    public function signup(FunctionalTester $I)
    {
        $I->sendPOST('/auth/signup', [
            'username' => 'Test_user',
            'email' => 'test@gmail.com',
            'password' => 'Net12345_'
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'User signed up successfully',
            'data' => [],
        ]);
    }

    /* ----------------------------------------------------
     * SIGN IN ERRORS
     * ---------------------------------------------------- */

    public function signinEmptyEmail(FunctionalTester $I)
    {
        $I->sendPOST('/auth/signin', []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1001,
            'message' => 'Email cannot be empty',
        ]);
    }

    public function signinEmptyPassword(FunctionalTester $I)
    {
        $I->sendPOST('/auth/signin', [
            'email' => 'test@gmail.com',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1002,
            'message' => 'Password cannot be empty',
        ]);
    }

    public function signinUserInactive(FunctionalTester $I)
    {
        $I->sendPOST('/auth/signin', [
            'email' => 'test@gmail.com',
            'password' => 'Net12345_'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 5001,
            'message' => 'User does not exist or inactive',
        ]);
    }


    /* ----------------------------------------------------
     * UPDATE STATUS
     * ---------------------------------------------------- */

    public function updateStatusUserCantUpdate(FunctionalTester $I)
    {
        $user = User::findByEmailAny('test@gmail.com');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/update-status/' . $user->id, [
            'status' => ''
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8007,
            'message' => 'User cannot make this action',
        ]);
    }

    public function updateStatusAdminCantUpdateOwnStatus(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->adminJwt);
        $I->sendPUT('/user/update-status/' . $this->adminId, []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8004,
            'message' => 'Admin cannot change info about itself',
        ]);
    }

    public function updateStatusEmptyStatus(FunctionalTester $I)
    {
        $user = User::findByEmailAny('test@gmail.com');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->adminJwt);
        $I->sendPUT('/user/update-status/' . $user->id, [
            'status' => ''
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1006,
            'message' => 'Status cannot be empty',
        ]);
    }

    public function updateStatusNotInRangeStatus(FunctionalTester $I)
    {
        $user = User::findByEmailAny('test@gmail.com');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->adminJwt);
        $I->sendPUT('/user/update-status/' . $user->id, [
            'status' => 100000
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2018,
            'message' => 'Status is not in range',
        ]);
    }

    public function updateStatus(FunctionalTester $I)
    {
        $user = User::findByEmailAny('test@gmail.com');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->adminJwt);
        $I->sendPUT('/user/update-status/' . $user->id, [
            'status' => 10
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'User status updated successfully',
            'data' => [],
        ]);
    }

    /* ----------------------------------------------------
     * SIGN IN SUCCESSFULLY
     * ---------------------------------------------------- */

    public function signin(FunctionalTester $I)
    {
        $I->sendPOST('/auth/signin', [
            'email' => 'test@gmail.com',
            'password' => 'Net12345_'
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'User signed in successfully',
            'data' => [
                'user' => [
                    // 'id',
                    // 'email',
                    // 'token',
                ],
                'refresh_token' => [],
            ],
        ]);

        $response = json_decode($I->grabResponse(), true);
        $this->newUserJwt = $response['data']['user']['token'] ?? null;
    }

    public function signinAdmin(FunctionalTester $I)
    {
        $I->sendPOST('/auth/signin', [
            'email' => 'test_admin@test.com',
            'password' => 'Net12345_'
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'User signed in successfully',
            'data' => [
                'user' => [
                    'id' => $this->adminId,
                    'email' => 'test_admin@test.com',
                ],
                'refresh_token' => [],
            ],
        ]);

        $response = json_decode($I->grabResponse(), true);
        $this->newAdminJwt = $response['data']['user']['token'] ?? null;
    }

    public function updateStatusOldAdminToken(FunctionalTester $I)
    {
        $user = User::findByEmailAny('test@gmail.com');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->adminJwt);
        $I->sendPUT('/user/update-status/' . $user->id, [
            'status' => 10
        ]);
        $I->seeResponseCodeIs(401);
        $I->seeResponseContainsJson([
            'success' => false,
            'message' => 'Your request was made with invalid credentials.',
            'error_code' => 401,
        ]);
    }

    // public function updateStatusNewAdminToken(FunctionalTester $I)
    // {
    //     $user = User::findByEmailAny('test@gmail.com');
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->newAdminJwt);
    //     $I->sendPUT('/user/update-status/' . $user->id, [
    //         'status' => 10
    //     ]);
    //     $I->seeResponseCodeIs(200);
    //     $I->seeResponseContainsJson([
    //         'success' => true,
    //         'message' => 'User status updated successfully',
    //         'data' => [],
    //     ]);

    //     $this->newUserJwt = SigninForm::generateJwt($user);
    // }

    /* ----------------------------------------------------
     * SIGN IN SUCCESSFULLY
     * ---------------------------------------------------- */

    public function logoutUserRoleCantLogoutSomeone(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->newUserJwt);
        $I->sendPUT('/auth/logout/' . $this->adminId, []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'success' => false,
            'message' => 'User cannot logout someone',
            'error_code' => 8006,
        ]);
    }

    public function logoutNewUser(FunctionalTester $I)
    {
        $user = User::findByEmailAny('test@gmail.com');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->newAdminJwt);
        $I->sendPUT('/auth/logout/' . $user->id, []);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'User logged out successfully',
        ]);
    }

    public function logoutAdminSelfLogout(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->newAdminJwt);
        $I->sendPUT('/auth/logout/' . $this->adminId, []);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'User logged out successfully',
        ]);
    }

    public function signinAdminUseOldToken(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->newAdminJwt);
        $I->sendPUT('/auth/logout/' . $this->adminId, []);
        $I->seeResponseCodeIs(401);
        $I->seeResponseContainsJson([
            'success' => false,
            'error_code' => 401,
            'message' => 'Your request was made with invalid credentials.',
        ]);
    }

    /* ----------------------------------------------------
     * UPDATE USER
     * ---------------------------------------------------- */

    public function updateUserNoAuth(FunctionalTester $I)
    {
        $I->sendPUT('/user/' . $this->staffId, [
            'username' => 'Updated_staff',
            'email' => 'updated_staff@test.com'
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function updateUserEmptyUsername(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/' . $this->staffId, [
            'username' => '',
            'email' => 'updated_staff@test.com'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1003,
            'message' => 'Username cannot be empty',
        ]);
    }

    public function updateUserInvalidUsernamePattern(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/' . $this->staffId, [
            'username' => 'abc',
            'email' => 'updated_staff@test.com'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2015,
            'message' => 'Invalid username pattern! starts with a letter, ≥ 5 chars, allowed characters include alphanumeric and underscore',
        ]);
    }

    public function updateUserEmptyEmail(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/' . $this->staffId, [
            'username' => 'Updated_staff',
            'email' => ''
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1001,
            'message' => 'Email cannot be empty',
        ]);
    }

    public function updateUserInvalidEmailFormat(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/' . $this->staffId, [
            'username' => 'Updated_staff',
            'email' => 'not_an_email'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2002,
            'message' => 'Invalid email format',
        ]);
    }

    public function updateUserEmailTaken(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/' . $this->staffId, [
            'username' => 'Updated_staff',
            'email' => 'test_admin@test.com'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 3001,
            'message' => 'Email has already been taken',
        ]);
    }

    public function updateUserUsernameTaken(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/' . $this->staffId, [
            'username' => 'Test_admin',
            'email' => 'updated_staff@test.com'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 3002,
            'message' => 'Username has already been taken',
        ]);
    }

    public function updateUserSuccess(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/' . $this->staffId, [
            'username' => 'Updated_staff',
            'email' => 'updated_staff@test.com'
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => [],
        ]);
    }

    public function updateUserCanUpdateOwnProfile(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/user/' . $this->programmerId, [
            'username' => 'Updated_programmer',
            'email' => 'updated_programmer@test.com'
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'User updated successfully',
        ]);
    }

    public function updateUserCantUpdateOwnProfileBecauseTokenInvalid(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/user/' . $this->programmerId, [
            'username' => 'Updated_programmerrr',
            'email' => 'updated_programmer@testtt.com'
        ]);
        $I->seeResponseCodeIs(401);
        $I->seeResponseContainsJson([
            'success' => false,
            'message' => 'Your request was made with invalid credentials.',
        ]);
    }

    public function updateUserNonAdminCanOnlyUpdateOwnProfile(FunctionalTester $I)
    {
        $user = User::findOne($this->staffId);
        $this->staffJwt = SigninForm::generateJwt($user);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/' . $this->programmerId, [
            'username' => 'Hacked_programmer',
            'email' => 'hacked@test.com'
        ]);
        $I->seeResponseCodeIs(400);
        // Non-admin updates their own profile regardless of ID in URL
        $I->seeResponseContainsJson([
            'success' => false,
            'error_code' => 8007,
            'message' => 'User cannot make this action',
        ]);
    }

    /* ----------------------------------------------------
     * UPDATE PASSWORD
     * ---------------------------------------------------- */

    public function updatePasswordNoAuth(FunctionalTester $I)
    {
        $I->sendPUT('/user/update-password', [
            'password' => 'Net12345_',
            'new_password' => 'NewPass123_',
            'confirm_password' => 'NewPass123_'
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function updatePasswordEmptyPassword(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/update-password', [
            'password' => '',
            'new_password' => 'NewPass123_',
            'confirm_password' => 'NewPass123_'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1002,
            'message' => 'Password cannot be empty',
        ]);
    }

    public function updatePasswordEmptyNewPassword(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/update-password', [
            'password' => 'Net12345_',
            'new_password' => '',
            'confirm_password' => ''
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1002,
            'message' => 'Password cannot be empty',
        ]);
    }

    public function updatePasswordEmptyConfirmPassword(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/update-password', [
            'password' => 'Net12345_',
            'new_password' => 'NewPass123_',
            'confirm_password' => ''
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1002,
            'message' => 'Password cannot be empty',
        ]);
    }

    public function updatePasswordInvalidCurrentPasswordFormat(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/update-password', [
            'password' => 'InvalidPass',
            'new_password' => 'NewPass123_',
            'confirm_password' => 'NewPass123_'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2016,
            'message' => 'Invalid password pattern! length ≥ 8 chars with upper and lower letters, digits and special characters',
        ]);
    }

    public function updatePasswordInvalidNewPasswordFormat(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/update-password', [
            'password' => 'Net12345_',
            'new_password' => 'weak',
            'confirm_password' => 'weak'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2016,
            'message' => 'Invalid password pattern! length ≥ 8 chars with upper and lower letters, digits and special characters',
        ]);
    }

    public function updatePasswordPasswordsNotEqual(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/update-password', [
            'password' => 'Net12345_',
            'new_password' => 'NewPass123_',
            'confirm_password' => 'DifferentPass123_'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2017,
            'message' => 'New password and confirmation password should be equal',
        ]);
    }

    public function updatePasswordSuccess(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/user/update-password', [
            'password' => 'Net12345_',
            'new_password' => 'NewPass123_',
            'confirm_password' => 'NewPass123_'
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'User password updated successfully',
            'data' => [],
        ]);
    }

    /* ----------------------------------------------------
     * DELETE USER
     * ---------------------------------------------------- */

    public function deleteUserNoAuth(FunctionalTester $I)
    {
        $I->sendDELETE('/user/' . $this->staffId, []);
        $I->seeResponseCodeIs(401);
    }

    public function deleteAdminCantDeleteItself(FunctionalTester $I)
    {
        $this->signinAdmin($I);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->newAdminJwt);
        $I->sendDELETE('/user/' . $this->adminId, []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8004,
            'message' => 'Admin cannot change info about itself',
        ]);
    }

    public function deleteUserCantDeleteAdmin(FunctionalTester $I)
    {
        $user = User::findOne($this->staffId);
        $this->staffJwt = SigninForm::generateJwt($user);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendDELETE('/user/' . $this->adminId, []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8007,
            'message' => 'User cannot make this action',
        ]);
    }

    public function deleteUserNonAdminCantDelete(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendDELETE('/user/' . $this->programmerId, []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8007,
            'message' => 'User cannot make this action',
        ]);
    }

    public function deleteUserNonAdminCanDeleteThemselves(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->secondProgrammerJwt);
        $I->sendDELETE('/user/' . $this->secondProgrammerId, []);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'User deleted successfully',
            'data' => [],
        ]);
    }

    public function deleteUserSuccess(FunctionalTester $I)
    {
        $user = new User();
        $user->email = 'temp_user@test.com';
        $user->username = 'Temp_user';
        $user->password_hash = Yii::$app->security->generatePasswordHash('Net12345_');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->role = 'USER';
        $user->status = $user::STATUS_ACTIVE;
        $user->save();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->newAdminJwt);
        $I->sendDELETE('/user/' . $user->id, []);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'User deleted successfully',
            'data' => [],
        ]);
    }
}