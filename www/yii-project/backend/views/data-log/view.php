<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Json;

/** @var yii\web\View $this */
/** @var common\models\DataLog $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Data Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="data-log-view">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <p>
        <!-- <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?> -->
    </p>

    <div class="card">
        <div class="card-header">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>

        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'user_id',
                    'model_id',
                    'model',
                    [
                        'attribute' => 'event',
                        'value' => function ($model) {
                                return $model->getEventName();
                            },
                    ],
                    [
                        'attribute' => 'old_attributes',
                        'format' => 'raw', // allows HTML formatting
                        'value' => function ($model) {
                                return '<pre>' . Json::encode(Json::decode($model->old_attributes), JSON_PRETTY_PRINT) . '</pre>';
                            },
                    ],
                    [
                        'attribute' => 'new_attributes',
                        'format' => 'raw',
                        'value' => function ($model) {
                                return '<pre>' . Json::encode(Json::decode($model->new_attributes), JSON_PRETTY_PRINT) . '</pre>';
                            },
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function ($model) {
                                return date('Y-m-d H:i:s', $model->created_at);
                            }
                    ],
                ],
            ]) ?>

        </div>
    </div>
</div>