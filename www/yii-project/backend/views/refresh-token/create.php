<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\UserRefreshToken $model */

$this->title = 'Create User Refresh Token';
$this->params['breadcrumbs'][] = ['label' => 'User Refresh Tokens', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-refresh-token-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
