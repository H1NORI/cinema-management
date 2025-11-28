<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\UserRefreshTokenSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-refresh-token-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'user_refresh_tokenID') ?>

    <?= $form->field($model, 'urf_userID') ?>

    <?= $form->field($model, 'urf_token') ?>

    <?= $form->field($model, 'urf_ip') ?>

    <?= $form->field($model, 'urf_user_agent') ?>

    <?php // echo $form->field($model, 'urf_created') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
