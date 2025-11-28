<?php

use common\models\DataLog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\DataLogSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var int $pageSize */

$this->title = 'Data Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="data-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- <p>
        <?= Html::a('Create Data Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // [
            //     'attribute'=> 'id',
            //     'headerOptions' => ['style' => 'width:100px'],
            // ],
            [
                'attribute'=> 'user_id',
                'headerOptions' => ['style' => 'width:100px'],
            ],
            [
                'attribute'=> 'model_id',
                'headerOptions' => ['style' => 'width:100px'],
            ],
            'model',
            [
                'attribute'=> 'event',
                'headerOptions' => ['style' => 'width:100px'],
                'value' => function ($model) {
                    return $model->getEventName();
                },
                'filter' => DataLog::getEventFilters()
            ],
            //'old_attributes:ntext',
            //'new_attributes:ntext',
            [
                'attribute'=> 'created_at',
                'value' => function ($model) {
                    return date('Y-m-d H:i:s', $model->created_at);
                }
            ],
            [
                'class' => ActionColumn::className(),
                'contentOptions' => ['style' =>'text-align: center;'],
                'template' => '{view}',
                'urlCreator' => function ($action, DataLog $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
        'tableOptions' => [
            'class' => 'table table-hover table-striped',
        ],
        'layout' => "
        <div class='card'>    
            <div class='card-header'>
                <div class='d-flex'>
                    <form method='get' class='d-flex align-items-center'>
                        <div>Show</div>
                        <select name='DataLogSearch[pageSize]' class='form-control float-right mx-2' onchange='this.form.submit()'>
                            <option value='10'" . ($pageSize === 10 ? 'selected' : '') . ">10</option>
                            <option value='20'" . ($pageSize === 20 ? 'selected' : '') . ">20</option>
                            <option value='50'" . ($pageSize === 50 ? 'selected' : '') . ">50</option>
                            <option value='100'" . ($pageSize === 100 ? 'selected' : '') . ">100</option>
                        </select>
                        <div>Entries</div>
                    </form>

                    <div class='input-group ml-auto' style='max-width: 200px;'>
                        <input type='text' name='table_search' class='form-control float-right' placeholder='Search'>

                        <div class='input-group-append'>
                            <button type='submit' class='btn btn-default'>
                                <i class='fas fa-search'></i>
                            </button>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class='card-body table-responsive p-0'>
                {items}
            </div>
            <div class='card-footer'>
                <div class='d-flex justify-content-between align-items-center'>
                    {summary}
                    {pager}
                </div>
            </div>
        </div>",
        'pager' => [
            'class' => \yii\bootstrap5\LinkPager::class,
            'options' => ['class' => 'pagination pagination-sm m-0 float-right'],
            'linkContainerOptions' => ['class' => 'page-item'],
            'linkOptions' => ['class' => 'page-link'],
        ],
    ]); ?>


</div>
