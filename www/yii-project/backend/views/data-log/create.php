<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DataLog $model */

$this->title = 'Create Data Log';
$this->params['breadcrumbs'][] = ['label' => 'Data Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="data-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
