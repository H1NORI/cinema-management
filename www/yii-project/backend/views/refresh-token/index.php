<?php

use common\models\UserRefreshToken;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\UserRefreshTokenSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var int $pageSize */

$this->title = 'User Refresh Tokens';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-refresh-token-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <!-- <p>
        <?= Html::a('Create User Refresh Token', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'user_refresh_tokenID',
                'headerOptions' => ['style' => 'width:100px'],
            ],
            [
                'attribute' => 'urf_userID',
                'headerOptions' => ['style' => 'width:100px'],
            ],
            'urf_token',
            'urf_user_agent',
            'urf_created',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, UserRefreshToken $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->user_refresh_tokenID]);
                    },
                'template' => '{delete}'
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
                        <select name='RefreshTokenSearch[pageSize]' class='form-control float-right mx-2' onchange='this.form.submit()'>
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