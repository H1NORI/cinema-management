<?php

namespace api\tests\functional;

use api\modules\v1\models\SigninForm;
use api\tests\FunctionalTester;
use common\models\Program;
use common\models\User;
use Yii;


class ProgramCest
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
        $user->username = 'test_admin';
        $user->password_hash = Yii::$app->security->generatePasswordHash('password123');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->role = 'ADMIN';
        $user->status = $user::STATUS_ACTIVE;
        $user->save();

        // Create Admin
        // $I->haveInDatabase('user', [
        //     'id' => $this->adminId,
        //     'username' => 'test_admin',
        //     'email' => 'test_admin@test.com',
        //     'auth_key' => Yii::$app->security->generateRandomString(),
        //     'password_hash' => Yii::$app->security->generatePasswordHash('password123'),
        //     'status' => 10,
        //     'role' => 'ADMIN',
        //     'created_at' => time(),
        //     'updated_at' => time(),
        // ]);

        $this->adminJwt = SigninForm::generateJwt($user);

        $user = User::findOne($this->programmerId) ?? new User();
        $user->id = $this->programmerId;
        $user->email = 'test_programmer@test.com';
        $user->username = 'test_programmer';
        $user->password_hash = Yii::$app->security->generatePasswordHash('password123');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->role = 'USER';
        $user->status = $user::STATUS_ACTIVE;
        $user->save();

        // // Create Programmer
        // $I->haveInDatabase('user', [
        //     'id' => $this->programmerId,
        //     'username' => 'test_programmer',
        //     'email' => 'test_programmer@test.com',
        //     'auth_key' => Yii::$app->security->generateRandomString(),
        //     'password_hash' => Yii::$app->security->generatePasswordHash('password123'),
        //     'status' => 10,
        //     'role' => 'USER',
        //     'created_at' => time(),
        //     'updated_at' => time(),
        // ]);

        $this->programmerJwt = SigninForm::generateJwt($user);

        $user = User::findOne($this->staffId) ?? new User();
        $user->id = $this->staffId;
        $user->email = 'test_staff@test.com';
        $user->username = 'test_staff';
        $user->password_hash = Yii::$app->security->generatePasswordHash('password123');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->role = 'USER';
        $user->status = $user::STATUS_ACTIVE;
        $user->save();

        // // Create Staff
        // $I->haveInDatabase('user', [
        //     'id' => $this->staffId,
        //     'username' => 'test_staff',
        //     'email' => 'test_staff@test.com',
        //     'auth_key' => Yii::$app->security->generateRandomString(),
        //     'password_hash' => Yii::$app->security->generatePasswordHash('password123'),
        //     'status' => 10,
        //     'role' => 'USER',
        //     'created_at' => time(),
        //     'updated_at' => time(),
        // ]);

        $this->staffJwt = SigninForm::generateJwt($user);


        $user = User::findOne($this->secondProgrammerId) ?? new User();
        $user->id = $this->secondProgrammerId;
        $user->email = 'test_second_programmer@test.com';
        $user->username = 'test_second_programmer';
        $user->password_hash = Yii::$app->security->generatePasswordHash('password123');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->role = 'USER';
        $user->status = $user::STATUS_ACTIVE;
        $user->save();

        $this->secondProgrammerJwt = SigninForm::generateJwt($user);

    }


    /* ----------------------------------------------------
     * CREATE PROGRAM
     * ---------------------------------------------------- */

    public function createProgramAsProgrammer(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPOST('/program', [
            'name' => 'Test Program',
            'description' => 'Desc',
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-05',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['message' => 'Program created successfully']);

        $response = json_decode($I->grabResponse(), true);
        $this->newProgramId = $response['data']['program']['id'] ?? null;
    }

    public function createProgramAsAdminNotAllowed(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->adminJwt);
        $I->sendPOST('/program');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8001,
            'message' => 'Admin cannot manage program',
        ]);
    }

    /* ----------------------------------------------------
     * PUBLIC ENDPOINTS
     * ---------------------------------------------------- */

    public function searchPrograms(FunctionalTester $I)
    {
        $I->sendGET('/program/search', ['name' => 'test']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['success' => true, 'message' => 'Programs retrived']);
    }

    public function viewProgram(FunctionalTester $I)
    {
        $I->sendGET('/program/' . $this->newProgramId);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Program retrived',
            'data' => [
                'program' => [
                    'id' => $this->newProgramId
                ]
            ]
        ]);
    }

    /* ----------------------------------------------------
     * UPDATE PROGRAM
     * ---------------------------------------------------- */

    public function updateProgramWithoutProgrammerRoleDenied(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/program/' . $this->newProgramId, ['name' => 'No access']);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 5002,
            'message' => 'Program does not exist',
        ]);
    }

    public function updateProgramNameRequired(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId, ['name' => null]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1008,
            'message' => 'Name cannot be empty',
        ]);
    }

    public function updateProgramNameTooLong(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId, ['name' => '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890']);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2010,
            'message' => 'Name max length is 255 characters',
        ]);
    }

    public function updateProgramStartDateRequired(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId, ['start_date' => null]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1004,
            'message' => 'Start date cannot be empty',
        ]);
    }

    public function updateProgramStartDateInvalidFormat(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId, ['start_date' => '12/07/2025']);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2003,
            'message' => 'Invalid start date format',
        ]);
    }

    public function updateProgramEndDateRequired(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId, ['end_date' => null]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1005,
            'message' => 'End date cannot be empty',
        ]);
    }

    public function updateProgramEndDateInvalidFormat(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId, ['end_date' => '12/07/2025']);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2004,
            'message' => 'Invalid end date format',
        ]);
    }

    public function updateProgram(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId, [
            'name' => 'Updated Program'
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Program updated successfully'
        ]);
    }

    /* ----------------------------------------------------
     * ADD PROGRAMMER
     * ---------------------------------------------------- */

    public function addProgrammer(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/add-programmer', [
            'user_id' => $this->secondProgrammerId,
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Program programmer added successfully'
        ]);
    }

    public function addProgrammerAddingAdminNotAllowed(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/add-programmer', [
            'user_id' => $this->adminId,
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8001,
            'message' => 'Admin cannot manage program',
        ]);
    }

    /* ----------------------------------------------------
     * ADD STAFF
     * ---------------------------------------------------- */

    public function addStaff(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/add-staff', [
            'user_id' => $this->staffId,
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Program staff added successfully'
        ]);
    }

    public function addStaffAddingAdminNotAllowed(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/add-staff', [
            'user_id' => $this->adminId,
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8001,
            'message' => 'Admin cannot manage program',
        ]);
    }

    /* ----------------------------------------------------
     * DELETE PROGRAM
     * ---------------------------------------------------- */

    public function deleteProgramAsStaffNotAllowed(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendDELETE('/program/' . $this->newProgramId);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 5002,
            'message' => 'Program does not exist',
        ]);
    }

    public function deleteProgramAsSecondProgrammerNotAllowed(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendDELETE('/program/' . $this->secondProgrammerId);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 5002,
            'message' => 'Program does not exist',
        ]);
    }

    public function deleteProgram(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendDELETE('/program/' . $this->newProgramId);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Program deleted successfully'
        ]);
    }

    public function deleteProgramInStateOtherFromCreated(FunctionalTester $I)
    {
        $this->createProgramAsProgrammer($I);
        $this->updateState($I);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendDELETE('/program/' . $this->newProgramId);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 7003,
            'message' => 'Can delete program only when state is CREATED',
        ]);

        Program::findOne($this->newProgramId)->delete();
    }

    /* ----------------------------------------------------
     * UPDATE STATE
     * ---------------------------------------------------- */

    public function updateState(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/update-state');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Program state updated successfully'
        ]);
    }
}