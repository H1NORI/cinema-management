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

    public function deletePrograms(FunctionalTester $I)
    {
        Program::deleteAll();
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
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action',
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
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action',
        ]);
    }

    public function deleteProgramAsSecondProgrammerNotAllowed(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendDELETE('/program/' . $this->secondProgrammerId);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action',
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
     * CREATE PROGRAM - VALIDATION TESTS
     * ---------------------------------------------------- */

    public function createProgramNameRequired(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPOST('/program', [
            'name' => null,
            'description' => 'Desc',
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-05',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1008,
            'message' => 'Name cannot be empty',
        ]);
    }

    public function createProgramNameTooLong(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPOST('/program', [
            'name' => str_repeat('a', 256),
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-05',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2010,
            'message' => 'Name max length is 255 characters',
        ]);
    }

    public function createProgramNameTaken(FunctionalTester $I)
    {
        // First create a program
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPOST('/program', [
            'name' => 'Unique Program Name',
            'description' => 'Desc',
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-05',
        ]);
        $I->seeResponseCodeIs(200);

        // Try to create another with the same name
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPOST('/program', [
            'name' => 'Unique Program Name',
            'description' => 'Desc',
            'start_date' => '2025-02-01',
            'end_date' => '2025-02-05',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 3003,
            'message' => 'Name has already been taken',
        ]);
    }

    public function createProgramStartDateRequired(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPOST('/program', [
            'name' => 'Test Program 1',
            'start_date' => null,
            'end_date' => '2025-01-05',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1004,
            'message' => 'Start date cannot be empty',
        ]);
    }

    public function createProgramStartDateInvalidFormat(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPOST('/program', [
            'name' => 'Test Program 1',
            'start_date' => '01-01-2025',
            'end_date' => '2025-01-05',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2003,
            'message' => 'Invalid start date format',
        ]);
    }

    public function createProgramEndDateRequired(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPOST('/program', [
            'name' => 'Test Program 1',
            'start_date' => '2025-01-01',
            'end_date' => null,
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1005,
            'message' => 'End date cannot be empty',
        ]);
    }

    public function createProgramEndDateInvalidFormat(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPOST('/program', [
            'name' => 'Test Program 1',
            'start_date' => '2025-01-01',
            'end_date' => '05-01-2025',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2004,
            'message' => 'Invalid end date format',
        ]);
    }

    /* ----------------------------------------------------
     * ADD PROGRAMMER - VALIDATION TESTS
     * ---------------------------------------------------- */

    public function addProgrammerUserIdRequired(FunctionalTester $I)
    {
        $this->createProgramAsProgrammer($I);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/add-programmer', [
            'user_id' => null,
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1007,
            'message' => 'User ID cannot be empty',
        ]);
    }

    public function addProgrammerUserDoesNotExist(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/add-programmer', [
            'user_id' => 999999888,
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 5001,
            'message' => 'User does not exist or inactive',
        ]);
    }

    public function addProgrammerNonProgrammerDenied(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/add-programmer', [
            'user_id' => $this->secondProgrammerId,
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            // 'error_code' => 5002,
            // 'message' => 'Program does not exist',
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action',
        ]);
    }

    public function addProgrammerProgramDoesNotExist(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/999999999/add-programmer', [
            'user_id' => $this->secondProgrammerId,
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action',
        ]);
    }

    /* ----------------------------------------------------
     * ADD STAFF - VALIDATION TESTS
     * ---------------------------------------------------- */

    public function addStaffUserIdRequired(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/add-staff', [
            'user_id' => null,
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1007,
            'message' => 'User ID cannot be empty',
        ]);
    }

    public function addStaffUserDoesNotExist(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/add-staff', [
            'user_id' => 999999888,
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 5001,
            'message' => 'User does not exist or inactive',
        ]);
    }

    public function addStaffNonProgrammerDenied(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/add-staff', [
            'user_id' => $this->staffId,
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action',
        ]);
    }

    public function addStaffProgramDoesNotExist(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/999999999/add-staff', [
            'user_id' => $this->staffId,
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action',
        ]);
    }

    /* ----------------------------------------------------
     * UPDATE PROGRAM - ADDITIONAL TESTS
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

    public function updateProgramDoesNotExist(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/999999999', [
            'name' => 'Updated',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action',
        ]);
    }

    public function updateProgramNonProgrammerDenied(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/program/' . $this->newProgramId, [
            'name' => 'Updated',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action',
        ]);
    }

    public function updateProgramDescriptionOptional(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId, [
            'description' => null,
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Program updated successfully'
        ]);
    }

    public function updateProgramMultipleFields(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId, [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'start_date' => '2025-02-01',
            'end_date' => '2025-02-15',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Program updated successfully'
        ]);
    }

    //todo review this
    // public function updateProgramInAnnouncedState(FunctionalTester $I)
    // {
    //     $this->updateState($I);
    //     $this->updateState($I); // Move to ANNOUNCED state

    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
    //     $I->sendPUT('/program/' . $this->newProgramId, [
    //         'name' => 'Should Fail',
    //     ]);
    //     $I->seeResponseCodeIs(400);
    //     $I->seeResponseContainsJson([
    //         'error_code' => 7001,
    //         'message' => 'Cant update program when state is ANNOUNCED',
    //     ]);

    //     Program::findOne($this->newProgramId)->delete();
    // }

    /* ----------------------------------------------------
     * DELETE PROGRAM - ADDITIONAL TESTS
     * ---------------------------------------------------- */

    public function deleteProgramDoesNotExist(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendDELETE('/program/999999999');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action',
        ]);
    }

    public function deleteProgramNonProgrammerDenied(FunctionalTester $I)
    {
        $this->createProgramAsProgrammer($I);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendDELETE('/program/' . $this->newProgramId);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action',
        ]);
    }

    /* ----------------------------------------------------
     * STATE TRANSITIONS
     * ---------------------------------------------------- */

    public function updateStateMultipleTimes(FunctionalTester $I)
    {
        // First state transition
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/update-state');
        $I->seeResponseCodeIs(200);

        // Second state transition
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/update-state');
        $I->seeResponseCodeIs(200);

        // Clean up
        Program::findOne($this->newProgramId)->delete();
    }

    public function updateStateNonProgrammerDenied(FunctionalTester $I)
    {
        $this->createProgramAsProgrammer($I);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/update-state');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action',
        ]);

        Program::findOne($this->newProgramId)->delete();
    }

    public function updateStateProgramDoesNotExist(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/999999999/update-state');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action',
        ]);
    }

    /* ----------------------------------------------------
     * VIEW PROGRAM - ADDITIONAL TESTS
     * ---------------------------------------------------- */

    public function viewProgramDoesNotExist(FunctionalTester $I)
    {
        $I->sendGET('/program/999999999');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 5002,
            'message' => 'Program does not exist',
        ]);
    }

    //todo review this test
    public function viewProgramPublicDataOnly(FunctionalTester $I)
    {
        $this->createProgramAsProgrammer($I);

        $I->sendGET('/program/' . $this->newProgramId);
        $I->seeResponseCodeIs(200);

        // $I->seeResponseContainsJson([
        //     'success' => true,
        //     'message' => 'Program retrived',
        //     'data' => [
        //         'program' => [
        //             'id' => $this->newProgramId
        //         ]
        //     ]
        // ]);


        // $response = json_decode($I->grabResponse(), true);

        // $this->assertArrayHasKey('id', $response['data']['program']);
        // $this->assertArrayHasKey('name', $response['data']['program']);
        // $this->assertArrayHasKey('description', $response['data']['program']);
        // $this->assertArrayHasKey('start_date', $response['data']['program']);
        // $this->assertArrayHasKey('end_date', $response['data']['program']);
    }

    /* ----------------------------------------------------
     * SEARCH PROGRAMS - ADDITIONAL TESTS
     * ---------------------------------------------------- */

    public function searchProgramsByName(FunctionalTester $I)
    {
        $I->sendGET('/program/search', [
            'name' => 'Non Existent Program',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Programs retrived'
        ]);
    }

    public function searchProgramsByDescription(FunctionalTester $I)
    {
        $I->sendGET('/program/search', [
            'description' => 'test',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Programs retrived'
        ]);
    }

    public function searchProgramsByDateRange(FunctionalTester $I)
    {
        $I->sendGET('/program/search', [
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Programs retrived'
        ]);
    }

    public function searchProgramsWithMultipleCriteria(FunctionalTester $I)
    {
        $I->sendGET('/program/search', [
            'name' => 'Program',
            'description' => 'test',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Programs retrived'
        ]);
    }

    /* ----------------------------------------------------
     * INDEX PROGRAMS
     * ---------------------------------------------------- */

    public function indexProgramsRequiresAuth(FunctionalTester $I)
    {
        $I->sendGET('/program');
        $I->seeResponseCodeIs(401);
    }

    public function indexProgramsAsAuthenticatedUser(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendGET('/program');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Programs retrived'
        ]);
    }

    /* ----------------------------------------------------
     * AUTHENTICATION TESTS
     * ---------------------------------------------------- */

    public function createProgramRequiresAuth(FunctionalTester $I)
    {
        $I->sendPOST('/program', [
            'name' => 'Test Program 1',
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-05',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function updateProgramRequiresAuth(FunctionalTester $I)
    {
        $I->sendPUT('/program/1', [
            'name' => 'Updated',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function deleteProgramRequiresAuth(FunctionalTester $I)
    {
        $I->sendDELETE('/program/1');
        $I->seeResponseCodeIs(401);
    }

    public function updateStateRequiresAuth(FunctionalTester $I)
    {
        $I->sendPUT('/program/1/update-state');
        $I->seeResponseCodeIs(401);
    }

    public function addProgrammerRequiresAuth(FunctionalTester $I)
    {
        $I->sendPUT('/program/1/add-programmer', [
            'user_id' => 1,
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function addStaffRequiresAuth(FunctionalTester $I)
    {
        $I->sendPUT('/program/1/add-staff', [
            'user_id' => 1,
        ]);
        $I->seeResponseCodeIs(401);
    }
}