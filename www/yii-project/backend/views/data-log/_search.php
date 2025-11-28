<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\DataLogSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="data-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'model_id') ?>

    <?= $form->field($model, 'model') ?>

    <?= $form->field($model, 'event') ?>

    <?php // echo $form->field($model, 'old_attributes') ?>

    <?php // echo $form->field($model, 'new_attributes') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
