<?php 

use yii\helpers\Html;
$dynamicItems = array_map(function ($link) {
    return [
        'label' => $link['label'],
        'header' => (bool)$link['is_header'],
        'icon' => $link['icon'],
        'iconStyle' => $link['icon_style'],
        'url' => $link['url'],
        'target' => $link['target'],
    ];
}, \common\models\Link::getSidebarLinks());
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <img src="<?=$assetDir?>/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Cinema management</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">


        <?php if (!Yii::$app->user->isGuest): ?>
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <?php
                    $avatar = Yii::$app->user->identity->avatar ?? $assetDir . '/img/user2-160x160.jpg';
                    ?>
                    <img src="<?= Html::encode($avatar) ?>"
                        class="img-circle elevation-2"
                        alt="<?= Html::encode(Yii::$app->user->identity->username) ?>">
                </div>
                <div class="info">
                    <a href="#" class="d-block">
                        <?= Html::encode(Yii::$app->user->identity->username) ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- SidebarSearch Form -->
        <!-- href be escaped -->
        <!-- <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div> -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <?php
            echo \hail812\adminlte\widgets\Menu::widget([
                'items' => array_merge([
                    ['label' => 'DATA', 'header' => true],
                    ['label' => 'Users',  'icon' => 'user', 'url' => ['/user/admin']],
                    // ['label' => 'Groups',  'icon' => 'users', 'url' => ['/group']],
                    // ['label' => 'Relationships',  'icon' => 'people-arrows', 'url' => ['/relationship']],
                    // ['label' => 'Focus Sessions',  'icon' => 'history', 'url' => ['/focus-session']],
                    // ['label' => 'Focus Schedules',  'icon' => 'calendar', 'url' => ['/focus-schedule']],
                    // ['label' => 'Tasks',  'icon' => 'tasks', 'url' => ['/task']],
                    // ['label' => 'RBAC',  'icon' => 'landmark', 'url' => ['/rbac']],
                    // ['label' => 'Refresh Tokens',  'icon' => 'fingerprint', 'url' => ['/refresh-token']],
                    ['label' => 'Links', 'icon' => 'link', 'url' => ['/link']],
                    ['label' => 'SETTINGS', 'header' => true],
                    ['label' => 'Gii',  'icon' => 'file-code', 'url' => ['/gii'], 'target' => '_blank'],
                    ['label' => 'Debug', 'icon' => 'bug', 'url' => ['/debug'], 'target' => '_blank'],
                    ['label' => 'Data Log', 'icon' => 'wrench', 'url' => ['/data-log'], 'target' => '_blank'],
                ],                    
                $dynamicItems)
            ]);
            ?>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>