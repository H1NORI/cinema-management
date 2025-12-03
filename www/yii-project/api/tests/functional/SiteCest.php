<?php

namespace api\tests\functional;

use api\modules\v1\models\SigninForm;
use api\tests\FunctionalTester;
use Yii;


class SiteCest
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

    public function loadIndex(FunctionalTester $I)
    {
        $I->sendGET('/site/index');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'API is working',
        ]);
    }

    public function loadToken(FunctionalTester $I)
    {

        $I->deleteHeader("Authorization");
        $I->amBearerAuthenticated($this->programmerJwt);
        $I->sendGET('/site/token');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Token parsed successfully',
            'received_token' => $this->programmerJwt
        ]);
    }

}