<?php

/** @var yii\web\View $this */
/** @var integer $user_count */

$this->title = 'Dashboard';
$this->params['breadcrumbs'] = [['label' => $this->title]];
?>
<div class="site-index">

    <div class="body-content">

        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                    <?= \hail812\adminlte\widgets\SmallBox::widget([
                        'title' => '54',
                        'text' => 'Sesions',
                        'icon' => 'fas fa-history',
                        // 'linkText' => 'View focus sessions',
                        'linkUrl' => '/focus-session',
                    ]) ?>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                    <?= \hail812\adminlte\widgets\SmallBox::widget([
                        'title' => '67',
                        'text' => 'Tasks',
                        'icon' => 'fas fa-tasks',
                        'theme' => 'success',
                        'linkUrl' => '/task',
                    ]) ?>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                    <?= \hail812\adminlte\widgets\SmallBox::widget([
                        'title' => $user_count,
                        'text' => 'User Registrations',
                        'icon' => 'fas fa-user-plus',
                        'theme' => 'warning',
                        // 'loadingStyle' => true
                        'linkUrl' => '/user/admin',
                    ]) ?>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                    <?= \hail812\adminlte\widgets\SmallBox::widget([
                        'title' => '44',
                        'text' => 'User Registrations',
                        'icon' => 'fas fa-user-plus',
                        'theme' => 'danger',
                        // 'loadingStyle' => true
                    ]) ?>
                </div>
            </div>

        </div>


    </div>
</div>