<?php

namespace common\components;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use common\models\DataLog;

class DataLogBehavior extends Behavior {

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'handleLog',
            ActiveRecord::EVENT_AFTER_UPDATE => 'handleLog',
            ActiveRecord::EVENT_AFTER_DELETE => 'handleLog',
        ];
    }

    public function handleLog($event) {
        $model = $event->sender;

        if (isset($this->ignoreLogBehavior) && $this->ignoreLogBehavior){
            return;
        }

        $oldAttributes = [];
        $newAttributes = [];

        if ($event->name === ActiveRecord::EVENT_AFTER_DELETE) {
            $oldAttributes = $model->toArray();
        } else if ($event->name === ActiveRecord::EVENT_AFTER_INSERT){
            $newAttributes = $model->attributes;
        } else {
            $oldAttributes = array_merge($model->oldAttributes, $event->changedAttributes);
            $newAttributes = $model->attributes;
        }

        DataLog::l(
            $oldAttributes,
            $newAttributes,
            $event,
            $model::className(),
            Yii::$app->user->id ?? null,
            // $this->getPK($model)
        );
    }

    private function getPK($model) {
        $pks = $model::getTableSchema()->primaryKey;

        if (count($pks) === 1)
            return $model->{$pks[0]};

        return null;
    }
}