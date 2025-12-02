<?php

namespace api\tests\functional;

use api\modules\v1\models\SigninForm;
use api\tests\FunctionalTester;


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
        // Create Submitter
        $I->haveInDatabase('user', [
            'id' => $this->adminId,
            'username' => 'test_admin',
            'email' => 'test_admin@test.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('password123'),
            'status' => 10,
            'role' => 'ADMIN',
        ]);

        $this->jwtAdmin = SigninForm::testGenerateJwt($this->adminId);

        // Create Programmer
        $I->haveInDatabase('user', [
            'id' => $this->programmerId,
            'username' => 'test_programmer',
            'email' => 'test_programmer@test.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('password123'),
            'status' => 10,
            'role' => 'USER',
        ]);

        $this->jwtProgrammer = SigninForm::testGenerateJwt($this->programmerId);

        // Create Admin
        $I->haveInDatabase('user', [
            'id' => $this->staffId,
            'username' => 'test_staff',
            'email' => 'test_staff@test.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('password123'),
            'status' => 10,
            'role' => 'USER',
        ]);

        $this->jwtStaff = SigninForm::testGenerateJwt($this->staffId);

    }

    /* ----------------------------------------------------
     * PUBLIC ENDPOINTS
     * ---------------------------------------------------- */

    
}