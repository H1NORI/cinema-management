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

    private string $newProgramId;


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

    public function updateStatusUserRole(FunctionalTester $I)
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


    /* ----------------------------------------------------
     * SIGN IN ERRORS
     * ---------------------------------------------------- */

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
                'user' => [],
            ],
        ]);
    }

    // public function updateProgramNameRequired(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendPUT('/program/' . $this->newProgramId, ['name' => null]);
    //     $I->seeResponseCodeIs(400);
    //     $I->seeResponseContainsJson([
    //         'error_code' => 1008,
    //         'message' => 'Name cannot be empty',
    //     ]);
    // }

    // public function updateProgramNameTooLong(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendPUT('/program/' . $this->newProgramId, ['name' => '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890']);
    //     $I->seeResponseCodeIs(400);
    //     $I->seeResponseContainsJson([
    //         'error_code' => 2010,
    //         'message' => 'Name max length is 255 characters',
    //     ]);
    // }

    // public function updateProgramStartDateRequired(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendPUT('/program/' . $this->newProgramId, ['start_date' => null]);
    //     $I->seeResponseCodeIs(400);
    //     $I->seeResponseContainsJson([
    //         'error_code' => 1004,
    //         'message' => 'Start date cannot be empty',
    //     ]);
    // }

    // public function updateProgramStartDateInvalidFormat(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendPUT('/program/' . $this->newProgramId, ['start_date' => '12/07/2025']);
    //     $I->seeResponseCodeIs(400);
    //     $I->seeResponseContainsJson([
    //         'error_code' => 2003,
    //         'message' => 'Invalid start date format',
    //     ]);
    // }

    // public function updateProgramEndDateRequired(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendPUT('/program/' . $this->newProgramId, ['end_date' => null]);
    //     $I->seeResponseCodeIs(400);
    //     $I->seeResponseContainsJson([
    //         'error_code' => 1005,
    //         'message' => 'End date cannot be empty',
    //     ]);
    // }

    // public function updateProgramEndDateInvalidFormat(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendPUT('/program/' . $this->newProgramId, ['end_date' => '12/07/2025']);
    //     $I->seeResponseCodeIs(400);
    //     $I->seeResponseContainsJson([
    //         'error_code' => 2004,
    //         'message' => 'Invalid end date format',
    //     ]);
    // }

    // public function updateProgram(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendPUT('/program/' . $this->newProgramId, [
    //         'name' => 'Updated Program'
    //     ]);

    //     $I->seeResponseCodeIs(200);
    //     $I->seeResponseContainsJson([
    //         'message' => 'Program updated successfully'
    //     ]);
    // }

    // /* ----------------------------------------------------
    //  * ADD PROGRAMMER
    //  * ---------------------------------------------------- */

    // public function addProgrammer(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendPUT('/program/' . $this->newProgramId . '/add-programmer', [
    //         'user_id' => $this->secondProgrammerId,
    //     ]);
    //     $I->seeResponseCodeIs(200);
    //     $I->seeResponseContainsJson([
    //         'message' => 'Program programmer added successfully'
    //     ]);
    // }

    // public function addProgrammerAddingAdminNotAllowed(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendPUT('/program/' . $this->newProgramId . '/add-programmer', [
    //         'user_id' => $this->adminId,
    //     ]);
    //     $I->seeResponseCodeIs(400);
    //     $I->seeResponseContainsJson([
    //         'error_code' => 8001,
    //         'message' => 'Admin cannot manage program',
    //     ]);
    // }

    // /* ----------------------------------------------------
    //  * ADD STAFF
    //  * ---------------------------------------------------- */

    // public function addStaff(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendPUT('/program/' . $this->newProgramId . '/add-staff', [
    //         'user_id' => $this->staffId,
    //     ]);
    //     $I->seeResponseCodeIs(200);
    //     $I->seeResponseContainsJson([
    //         'message' => 'Program staff added successfully'
    //     ]);
    // }

    // public function addStaffAddingAdminNotAllowed(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendPUT('/program/' . $this->newProgramId . '/add-staff', [
    //         'user_id' => $this->adminId,
    //     ]);
    //     $I->seeResponseCodeIs(400);
    //     $I->seeResponseContainsJson([
    //         'error_code' => 8001,
    //         'message' => 'Admin cannot manage program',
    //     ]);
    // }

    // /* ----------------------------------------------------
    //  * DELETE PROGRAM
    //  * ---------------------------------------------------- */

    // public function deleteProgramAsStaffNotAllowed(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
    //     $I->sendDELETE('/program/' . $this->newProgramId);
    //     $I->seeResponseCodeIs(400);
    //     $I->seeResponseContainsJson([
    //         'error_code' => 5002,
    //         'message' => 'Program does not exist',
    //     ]);
    // }

    // public function deleteProgramAsSecondProgrammerNotAllowed(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
    //     $I->sendDELETE('/program/' . $this->secondProgrammerId);
    //     $I->seeResponseCodeIs(400);
    //     $I->seeResponseContainsJson([
    //         'error_code' => 5002,
    //         'message' => 'Program does not exist',
    //     ]);
    // }

    // public function deleteProgram(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendDELETE('/program/' . $this->newProgramId);
    //     $I->seeResponseCodeIs(200);
    //     $I->seeResponseContainsJson([
    //         'message' => 'Program deleted successfully'
    //     ]);
    // }

    // public function deleteProgramInStateOtherFromCreated(FunctionalTester $I)
    // {
    //     $this->createProgramAsProgrammer($I);
    //     $this->updateState($I);

    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendDELETE('/program/' . $this->newProgramId);
    //     $I->seeResponseCodeIs(400);
    //     $I->seeResponseContainsJson([
    //         'error_code' => 7003,
    //         'message' => 'Can delete program only when state is CREATED',
    //     ]);

    //     Program::findOne($this->newProgramId)->delete();
    // }

    // /* ----------------------------------------------------
    //  * UPDATE STATE
    //  * ---------------------------------------------------- */

    // public function updateState(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendPUT('/program/' . $this->newProgramId . '/update-state');
    //     $I->seeResponseCodeIs(200);
    //     $I->seeResponseContainsJson([
    //         'message' => 'Program state updated successfully'
    //     ]);
    // }
}