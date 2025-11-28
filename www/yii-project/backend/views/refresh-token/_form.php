<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\UserRefreshToken $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-refresh-token-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'urf_userID')->textInput() ?>

    <?= $form->field($model, 'urf_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'urf_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'urf_user_agent')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'urf_created')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
