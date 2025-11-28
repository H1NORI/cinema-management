<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var yii\gii\generators\crud\Generator $generator */

$modelClass = StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

use <?= $generator->modelClass ?>;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>

/** @var yii\web\View $this */
<?= !empty($generator->searchModelClass) ? "/** @var " . ltrim($generator->searchModelClass, '\\') . " \$searchModel */\n" : '' ?>
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var int $pageSize */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

    <!-- <h1><?= "<?= " ?>Html::encode($this->title) ?></h1> -->

    <!-- <p>
        <?= "<?= " ?>Html::a(<?= $generator->generateString('Create ' . Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>, ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

<?= $generator->enablePjax ? "    <?php Pjax::begin(); ?>\n" : '' ?>
<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= "<?= " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            //'" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            //'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, <?= $modelClass ?> $model, $key, $index, $column) {
                    return Url::toRoute([$action, <?= $generator->generateUrlParams() ?>]);
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
                        <select name='TaskSearch[pageSize]' class='form-control float-right mx-2' onchange='this.form.submit()'>
                            <option value='10' <?= "<?= \$pageSize === 10 ? 'selected' : '' ?>" ?>10</option>
                            <option value='20' <?= "<?= \$pageSize === 20 ? 'selected' : '' ?>" ?>20</option>
                            <option value='50' <?= "<?= \$pageSize === 50 ? 'selected' : '' ?>" ?>50</option>
                            <option value='100' <?= "<?= \$pageSize === 100 ? 'selected' : '' ?>" ?>100</option>
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
<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $generator->getNameAttribute() ?>), ['view', <?= $generator->generateUrlParams() ?>]);
        },
    ]) ?>
<?php endif; ?>

<?= $generator->enablePjax ? "    <?php Pjax::end(); ?>\n" : '' ?>

</div>
