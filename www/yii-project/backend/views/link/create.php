<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\LinkForm $model */

$this->title = 'Create Link';
$this->params['breadcrumbs'][] = ['label' => 'Links', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="link-form-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
