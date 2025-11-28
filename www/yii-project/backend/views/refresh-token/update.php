<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\UserRefreshToken $model */

$this->title = 'Update User Refresh Token: ' . $model->user_refresh_tokenID;
$this->params['breadcrumbs'][] = ['label' => 'User Refresh Tokens', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user_refresh_tokenID, 'url' => ['view', 'user_refresh_tokenID' => $model->user_refresh_tokenID]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-refresh-token-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
