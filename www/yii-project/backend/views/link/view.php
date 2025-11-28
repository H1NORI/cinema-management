<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\LinkForm $model */

$this->title = $model->label;
$this->params['breadcrumbs'][] = [
    'label' =>
        'Links',
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="link-form-view">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
                    'id',
                    'label',
                    [
                        'attribute' => 'is_header',
                        'headerOptions' => ['style' => 'width:100px'],
                        'value' => function ($model) {
                                return $model->getIsHeaderNameBadge();
                            },
                        'format' => 'raw',
                    ],
                    'url:url',
                    'priority',
                    'icon',
                    'icon_style',
                    'target',
                    [
                        'attribute' => 'status',
                        'headerOptions' => ['style' => 'width:100px'],
                        'value' => function ($model) {
                                return $model->getStatusNameBadge();
                            },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function ($model) {
                                return date('Y-m-d H:i:s', $model->created_at);
                            }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => function ($model) {
                                return date('Y-m-d H:i:s', $model->updated_at);
                            }
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>