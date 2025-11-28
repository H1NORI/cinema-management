<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DataLog $model */

$this->title = 'Update Data Log: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Data Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="data-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
