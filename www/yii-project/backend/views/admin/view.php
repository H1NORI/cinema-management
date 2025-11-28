<?php

use common\models\User;
use dektrium\user\models\Profile;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var User $model */
/** @var Profile $profile */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="task-form-view">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('View Sessions', ['/focus-session/index', 'FocusSessionSearch' => ['user_id' => $model->id]], ['class' => 'btn btn-success']) ?>
        <?= Html::a('View Focus Schedules', ['/focus-schedule/index', 'FocusScheduleSearch' => ['user_id' => $model->id]], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('View Tasks', ['/task/index', 'TaskSearch' => ['user_id' => $model->id]], ['class' => 'btn btn-danger']) ?>
        <!-- <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
                    'username',
                    'email',
                    [
                        'attribute' => 'name',
                        'value' => $profile->name
                    ],
                    [
                        'attribute' => 'public_email',
                        'value' => $profile->public_email
                    ],
                    [
                        'attribute' => 'gravatar_email',
                        'value' => $profile->gravatar_email
                    ],
                    [
                        'attribute' => 'gravatar_id',
                        'value' => $profile->gravatar_id
                    ],
                    [
                        'attribute' => 'location',
                        'value' => $profile->location
                    ],
                    [
                        'attribute' => 'website',
                        'value' => $profile->website
                    ],
                    [
                        'attribute' => 'bio',
                        'value' => $profile->bio
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