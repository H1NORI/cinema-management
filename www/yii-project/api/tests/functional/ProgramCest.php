<?php

namespace api\tests\functional;

use api\modules\v1\models\SigninForm;
use api\tests\FunctionalTester;
use Yii;


class ProgramCest
{

    private int $adminId = 999999;
    private int $programmerId = 999991;
    private int $staffId = 999992;

    private string $adminJwt;
    private string $programmerJwt;
    private string $staffJwt;


    private static bool $initialized = false;

    public function _before(FunctionalTester $I)
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

    /* ----------------------------------------------------
     * PUBLIC ENDPOINTS
     * ---------------------------------------------------- */


    private function createTestUsers(FunctionalTester $I)
    {
        // Create Admin
        $I->haveInDatabase('user', [
            'id' => $this->adminId,
            'username' => 'test_admin',
            'email' => 'test_admin@test.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('password123'),
            'status' => 10,
            'role' => 'ADMIN',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->adminJwt = SigninForm::testGenerateJwt($this->adminId);

        // Create Programmer
        $I->haveInDatabase('user', [
            'id' => $this->programmerId,
            'username' => 'test_programmer',
            'email' => 'test_programmer@test.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('password123'),
            'status' => 10,
            'role' => 'USER',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->programmerJwt = SigninForm::testGenerateJwt($this->programmerId);

        // Create Staff
        $I->haveInDatabase('user', [
            'id' => $this->staffId,
            'username' => 'test_staff',
            'email' => 'test_staff@test.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('password123'),
            'status' => 10,
            'role' => 'USER',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->staffJwt = SigninForm::testGenerateJwt($this->staffId);

    }

    /* ----------------------------------------------------
     * PUBLIC ENDPOINTS
     * ---------------------------------------------------- */

    // public function listPrograms(FunctionalTester $I)
    // {
    //     $I->sendGET('/program');
    //     $I->seeResponseCodeIs(200);
    //     $I->seeResponseIsJson();
    //     $I->seeResponseContainsJson(['success' => true]);
    // }

    public function searchPrograms(FunctionalTester $I)
    {
        $I->sendGET('/program/search', ['name' => 'test']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['success' => true]);
    }

    public function viewProgram(FunctionalTester $I)
    {
        $I->sendGET('/program/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['success' => true]);
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
    }

    public function createProgramAsAdminNotAllowed(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->adminJwt);
        $I->sendPOST('/program');

        $I->seeResponseCodeIs(400);
        // $I->seeResponseContainsJson([
        //     'message' => 'ADMIN_CANT_MANAGE_PROGRAM'
        // ]);
    }

    /* ----------------------------------------------------
     * UPDATE PROGRAM
     * ---------------------------------------------------- */

    public function updateProgramWithoutProgrammerRoleDenied(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/program/1', ['name' => 'No access']);

        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'message' => 'PROGRAMER_ROLE_REQUIRED'
        ]);
    }

    public function updateProgram(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/1', [
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

    // public function addProgrammer(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->jwtProgrammer());

    //     $I->sendPUT('/program/1/add-programmer', [
    //         'user_id' => 40
    //     ]);

    //     $I->seeResponseCodeIs(200);
    //     $I->seeResponseContainsJson([
    //         'message' => 'Program updated successfully'
    //     ]);
    // }

    // /* ----------------------------------------------------
    //  * ADD STAFF
    //  * ---------------------------------------------------- */

    // public function addStaff(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->jwtProgrammer());

    //     $I->sendPUT('/program/1/add-staff', [
    //         'user_id' => 50
    //     ]);

    //     $I->seeResponseCodeIs(200);
    //     $I->seeResponseContainsJson([
    //         'message' => 'Program updated successfully'
    //     ]);
    // }

    // /* ----------------------------------------------------
    //  * DELETE PROGRAM
    //  * ---------------------------------------------------- */

    // public function deleteProgram(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->jwtProgrammer());

    //     $I->sendDELETE('/program/1');

    //     $I->seeResponseCodeIs(200);
    //     $I->seeResponseContainsJson([
    //         'message' => 'Program deleted successfully'
    //     ]);
    // }

    // /* ----------------------------------------------------
    //  * UPDATE STATE
    //  * ---------------------------------------------------- */

    // public function updateState(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->jwtProgrammer());

    //     $I->sendPUT('/program/1/update-state');

    //     $I->seeResponseCodeIs(200);
    //     $I->seeResponseContainsJson([
    //         'message' => 'Program state updated successfully'
    //     ]);
    // }
}