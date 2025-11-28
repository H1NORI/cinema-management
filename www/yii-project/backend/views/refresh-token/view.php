<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\UserRefreshToken $model */

$this->title = $model->user_refresh_tokenID;
$this->params['breadcrumbs'][] = ['label' => 'User Refresh Tokens', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-refresh-token-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'user_refresh_tokenID' => $model->user_refresh_tokenID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'user_refresh_tokenID' => $model->user_refresh_tokenID], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="card">
        <div class="card-header">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>

        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'user_refresh_tokenID',
                    'urf_userID',
                    'urf_token',
                    'urf_ip',
                    'urf_user_agent',
                    'urf_created',
                ],
            ]) ?>

        </div>
    </div>
</div>