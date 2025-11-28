<?php

namespace common\models;

use common\components\DataLogBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "link".
 *
 * @property int $id
 * @property string $label
 * @property int|null $is_header
 * @property string|null $url
 * @property int $priority
 * @property string|null $icon
 * @property string|null $icon_style
 * @property string|null $target
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class Link extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'link';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            // DataLogBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['icon', 'icon_style', 'target'], 'default', 'value' => null],
            [['status',], 'default', 'value' => self::STATUS_ACTIVE],
            [['priority', 'is_header'], 'default', 'value' => 0],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            ['is_header', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['label', 'is_header', 'priority'], 'required'],
            [['priority', 'status', 'created_at', 'updated_at'], 'integer'],
            [['label', 'url', 'icon', 'icon_style', 'target'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label' => 'Label',
            'is_header' => 'Header',
            'url' => 'Url',
            'priority' => 'Priority',
            'icon' => 'Icon',
            'icon_style' => 'Icon Style',
            'target' => 'Target',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getSidebarLinks(): array
    {
        return \Yii::$app->cache->getOrSet('sidebar_links', function () {
            return self::find()
                ->where(['status' => self::STATUS_ACTIVE])
                ->orderBy(['priority' => SORT_DESC])
                ->asArray()
                ->all();
        }, 3600);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->cache->delete('sidebar_links');
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Yii::$app->cache->delete('sidebar_links');
    }

}
