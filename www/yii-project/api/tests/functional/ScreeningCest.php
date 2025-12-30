<?php

namespace api\tests\functional;

use api\modules\v1\models\SigninForm;
use api\tests\FunctionalTester;
use common\models\Program;
use common\models\Screening;
use common\models\User;
use Yii;


class ScreeningCest
{

    private int $adminId = 999999;
    private int $programmerId = 999991;
    private int $staffId = 999992;

    private int $secondProgrammerId = 999993;
    private int $secondStaffId = 999994;


    private string $adminJwt;
    private string $programmerJwt;
    private string $staffJwt;
    private string $secondProgrammerJwt;
    private string $secondStaffJwt;

    private string $newProgramId;
    private string $newScreeningId;
    private string $newSecondScreeningId;


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
        $user->access_token = SigninForm::generateJwt($user);
        $this->adminJwt = $user->access_token;
        $user->save();


        $user = User::findOne($this->programmerId) ?? new User();
        $user->id = $this->programmerId;
        $user->email = 'test_programmer@test.com';
        $user->username = 'Test_programmer';
        $user->password_hash = Yii::$app->security->generatePasswordHash('Net12345_');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->role = 'USER';
        $user->status = $user::STATUS_ACTIVE;
        $user->access_token = SigninForm::generateJwt($user);
        $this->programmerJwt = $user->access_token;
        $user->save();


        $user = User::findOne($this->staffId) ?? new User();
        $user->id = $this->staffId;
        $user->email = 'test_staff@test.com';
        $user->username = 'Test_staff';
        $user->password_hash = Yii::$app->security->generatePasswordHash('Net12345_');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->role = 'USER';
        $user->status = $user::STATUS_ACTIVE;
        $user->access_token = SigninForm::generateJwt($user);
        $this->staffJwt = $user->access_token;
        $user->save();


        $user = User::findOne($this->secondProgrammerId) ?? new User();
        $user->id = $this->secondProgrammerId;
        $user->email = 'test_second_programmer@test.com';
        $user->username = 'Test_second_programmer';
        $user->password_hash = Yii::$app->security->generatePasswordHash('Net12345_');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->role = 'USER';
        $user->status = $user::STATUS_ACTIVE;
        $user->access_token = SigninForm::generateJwt($user);
        $this->secondProgrammerJwt = $user->access_token;
        $user->save();

        $user = User::findOne($this->secondStaffId) ?? new User();
        $user->id = $this->secondStaffId;
        $user->email = 'test_second_staff@test.com';
        $user->username = 'Test_second_staff';
        $user->password_hash = Yii::$app->security->generatePasswordHash('Net12345_');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->role = 'USER';
        $user->status = $user::STATUS_ACTIVE;
        $user->access_token = SigninForm::generateJwt($user);
        $this->secondStaffJwt = $user->access_token;
        $user->save();
    }


    public function deleteScreenings(FunctionalTester $I)
    {
        Screening::deleteAll();
    }

    public function deletePrograms(FunctionalTester $I)
    {
        Program::deleteAll();
    }


    /* ----------------------------------------------------
     * PREPARE PROGRAM
     * ---------------------------------------------------- */

    public function createProgramAsProgrammer(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPOST('/program', [
            'name' => 'Screening Test Program',
            'description' => 'Desc',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['message' => 'Program created successfully']);

        $response = json_decode($I->grabResponse(), true);
        $this->newProgramId = $response['data']['program']['id'] ?? null;
    }

    public function programAddStaff(FunctionalTester $I)
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

    public function programAddSecondStaff(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/add-staff', [
            'user_id' => $this->secondStaffId,
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Program staff added successfully'
        ]);
    }

    public function programAddProgrammer(FunctionalTester $I)
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

    /* ----------------------------------------------------
     * CREATE SCREENING
     * ---------------------------------------------------- */

    public function createScreeningRequiresAuth(FunctionalTester $I)
    {
        $I->sendPOST('/screening', [
            'program_id' => 1,
            'film_title' => 'Test Film',
            'film_duration' => 90,
            'auditorium' => 'Main',
            'start_time' => '2025-01-01 10:00:00',
            'end_time' => '2025-01-01 11:30:00',
        ]);
        $I->seeResponseCodeIs(401);
    }

    //todo возможно стоит убрать чтобы не докапались до того что может создвть только staff
    public function createScreeningAsProgrammer(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->secondProgrammerJwt);
        $I->sendPOST('/screening', [
            'program_id' => $this->newProgramId,
            'film_title' => 'Test Film',
            'film_duration' => 90,
            'auditorium' => 'Main',
            'start_time' => '10:00:00',
            'end_time' => '11:30:00',
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8008,
            'message' => 'Staff role required to make this action',
        ]);
    }

    public function createScreeningRequiredProgramID(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPOST('/screening', [
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1009,
            'message' => 'Program ID cannot be empty',
        ]);
    }

    public function createScreeningProgramDoesntExists(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPOST('/screening', [
            'program_id' => 999999999999,
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8008,
            'message' => 'Staff role required to make this action',
        ]);
    }

    public function createScreening(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPOST('/screening', [
            'program_id' => $this->newProgramId,
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Screening created successfully',
        ]);

        $response = json_decode($I->grabResponse(), true);
        $this->newScreeningId = $response['data']['screening']['id'] ?? null;
    }


    public function submitScreeningProgramNotInSubmission(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/submit');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 7006,
            'message' => 'Program is not in SUBMISION state',
        ]);
    }

    public function programUpdateState(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->secondProgrammerJwt);
        $I->sendPUT('/program/' . $this->newProgramId . '/update-state', [
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Program state updated successfully',
        ]);
    }

    public function submitScreeningMissingStartTime(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/submit');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1010,
            'message' => 'Start time cannot be empty',
        ]);
    }

    public function updateScreeningStartTimeInvalidFormat(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId, [
            'start_time' => '10:00'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2006,
            'message' => 'Invalid start time format',
        ]);
    }

    public function updateScreeningStartTime(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId, [
            'start_time' => '10:00:00'
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Screening updated successfully',
        ]);
    }

    public function submitScreeningMissingEndTime(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/submit');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1011,
            'message' => 'End time cannot be empty',
        ]);
    }

    public function updateScreeningEndTimeInvalidFormat(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId, [
            'end_time' => '10:00'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2007,
            'message' => 'Invalid end time format',
        ]);
    }

    public function updateScreeningEndTimeMustBeHigher(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId, [
            'end_time' => '10:00:00'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2020,
            'message' => 'Start time must be before end time',
        ]);
    }

    public function updateScreeningEndTime(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId, [
            'end_time' => '11:35:00'
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Screening updated successfully',
        ]);
    }

    public function submitScreeningMissingFilmTitle(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/submit');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1017,
            'message' => 'Film title cannot be empty',
        ]);
    }

    public function createSecondScreening(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPOST('/screening', [
            'program_id' => $this->newProgramId,
            'film_title' => 'back to the future',
            'start_time' => '12:00:00',
            'end_time' => '13:30:00',
            'auditorium' => 'Main Hall',
            'film_duration' => 90,
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Screening created successfully',
        ]);

        $response = json_decode($I->grabResponse(), true);
        $this->newSecondScreeningId = $response['data']['screening']['id'] ?? null;
    }

    public function updateScreeningFilmTitleCanBeSame(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId, [
            'film_title' => 'back to the future',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Screening updated successfully',
        ]);
    }

    public function updateScreeningFilmTitle(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId, [
            'film_title' => 'Unique title',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Screening updated successfully',
        ]);
    }

    public function submitScreeningMissingAuditorium(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/submit');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1018,
            'message' => 'Auditorium cannot be empty',
        ]);
    }

    public function updateScreeningAuditorium(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId, [
            'auditorium' => 'first',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Screening updated successfully',
        ]);
    }

    public function submitScreeningMissingFilmDuration(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/submit');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1016,
            'message' => 'Film duration cannot be empty',
        ]);
    }

    public function updateScreeningFilmDurationInvalidTimeRange(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId, [
            'film_duration' => 100,
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'message' => 'End time must be greater or equal to film duration',
        ]);
    }

    public function updateScreeningFilmDuration(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId, [
            'film_duration' => 90,
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Screening updated successfully',
        ]);
    }

    public function submitScreeningSuccessfully(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/submit');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Screening submitted successfully',
        ]);
    }

    public function assignHandlerRequiresProgrammerRole(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/assign-handler', ['handler_id' => $this->secondStaffId]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action',
        ]);
    }

    public function assignHandlerRequiresProgramAssigmentState(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/assign-handler', ['handler_id' => $this->secondStaffId]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 7007,
            'message' => 'Program is not in ASSIGNMENT state',
        ]);
    }

    public function assignHandlerRequiresProgrammer(FunctionalTester $I)
    {
        $this->programUpdateState($I);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/assign-handler', [
            'handler_id' => $this->secondStaffId
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Screening handler assigned successfully',
        ]);
    }

    public function reviewRequiresAuthAsHandler(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/review', []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 5002,
            'message' => 'Program does not exist'
        ]);
    }

    public function reviewRequiresProgramReviewState(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->secondStaffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/review', []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 7009,
            'message' => 'Program is not in REVIEW state'
        ]);
    }

    public function reviewRequiresScore(FunctionalTester $I)
    {
        $this->programUpdateState($I);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->secondStaffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/review', []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1013,
            'message' => 'Score cannot be empty'
        ]);
    }

    public function reviewScoreInvalidRange(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->secondStaffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/review', [
            'score' => 999
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 2022,
            'message' => 'Invalid score range must be beetwen 0 and 100'
        ]);
    }

    public function reviewRequiresComments(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->secondStaffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/review', [
            'score' => 67
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 1014,
            'message' => 'Comments cannot be empty'
        ]);
    }

    public function reviewSuccessfull(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->secondStaffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/review', [
            'score' => 67,
            'comments' => 'Here is some comments about film'
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Screening review added successfully'
        ]);
    }

    public function withdrawScreeningRequiresProgramSubmissionOrCreatedState(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendDelete('/screening/' . $this->newSecondScreeningId . '/withdraw');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 7014,
            'message' => 'Program is not in SUBMISION or CREATED state'
        ]);
    }

    public function approveRequiresProgramSchedulingOrDecisionState(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/approve');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 7010,
            'message' => 'Program is not in SCHEDULING or DECISION state'
        ]);
    }

    public function approveRequiresSubmitterRole(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/approve');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 5002,
            'message' => 'Program does not exist'
        ]);
    }

    public function approveSuccessfull(FunctionalTester $I)
    {
        $this->programUpdateState($I);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/approve');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'message' => 'Screening approved successfully'
        ]);
    }

    public function rejectRequiresProgrammerRole(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/reject', [
            'rejection_reason' => 'Not good'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 8002,
            'message' => 'Programmer role required to make this action'
        ]);
    }

    public function finalSubmitRequiresSubmitterRole(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->programmerJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/final-submit');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 5002,
            'message' => 'Program does not exist'
        ]);
    }

    public function finalSubmitRequiresProgramFinalPublicationState(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
        $I->sendPUT('/screening/' . $this->newScreeningId . '/final-submit');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error_code' => 7012,
            'message' => 'Program is not in FINAL_PUBLICATION state'
        ]);
    }

    // public function acceptRequiresProgrammerRole(FunctionalTester $I)
    // {
    //     $I->haveHttpHeader('Authorization', 'Bearer ' . $this->staffJwt);
    //     $I->sendPUT('/screening/' . $this->newScreeningId . '/accept');
    //     $I->seeResponseCodeIs(400);
    //     $I->seeResponseContainsJson([
    //         'error_code' => 8002,
    //         'message' => 'Programmer role required to make this action'
    //     ]);
    // }




}