<?php
namespace api\modules\v1\models;

use Yii;
use yii\base\BaseObject;
use yii\filters\RateLimitInterface;

class GuestIdentity extends BaseObject implements RateLimitInterface
{
    public function getRateLimit($request, $action)
    {
        if (YII_ENV_DEV || YII_ENV_TEST) {
            return [100000, 60];
        }

        // [запросов, за сколько секунд]
        return [50, 60]; // 50 запросов в минуту
    }

    public function loadAllowance($request, $action)
    {
        $key = $this->key();
        return Yii::$app->cache->get($key) ?: [50, time()];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        Yii::$app->cache->set($this->key(), [$allowance, $timestamp], 60);
    }

    private function key()
    {
        return 'rate_limit_guest_' . Yii::$app->request->userIP;
    }
}
