<?php

namespace common\models;

use Yii;
use yii\helpers\Json;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "data_log".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $model_id
 * @property string|null $model
 * @property int $event
 * @property string|null $old_attributes
 * @property string|null $new_attributes
 * @property int $created_at
 *
 * @property User $user
 */
class DataLog extends ActiveRecord
{

    const EVENT_INSERT = 1;
    const EVENT_UPDATE = 2;
    const EVENT_DELETE = 3;

    const EVENT_MAP = [
        ActiveRecord::EVENT_AFTER_INSERT => DataLog::EVENT_INSERT,
        ActiveRecord::EVENT_AFTER_UPDATE => DataLog::EVENT_UPDATE,
        ActiveRecord::EVENT_AFTER_DELETE => DataLog::EVENT_DELETE,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'data_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'model_id', 'model', 'old_attributes', 'new_attributes'], 'default', 'value' => null],
            [['event', 'created_at'], 'required'],
            [['user_id', 'model_id', 'event', 'created_at'], 'integer'],
            [['old_attributes', 'new_attributes'], 'string'],
            [['model'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'model_id' => 'Model ID',
            'model' => 'Model',
            'event' => 'Event',
            'old_attributes' => 'Old Attributes',
            'new_attributes' => 'New Attributes',
            'created_at' => 'Created At',
        ];
    }

    static function l(array $oldAttributes, array $newAttributes, $event, $sourceModel, $user_id = null, $model_id = null) {
        $log = new self;
        $sender = $event->sender;

        if (isset($sender->logIgnoredAttributes) && is_array($sender->logIgnoredAttributes)) {
            foreach ($sender->logIgnoredAttributes as $attr) {
                unset($oldAttributes[$attr], $newAttributes[$attr]);
            }
        }

        $log->user_id = $user_id;
        $log->model_id = $model_id;
        $log->model = $sourceModel;
        $log->event = self::EVENT_MAP[$event->name] ?? 0;
        $log->old_attributes = Json::encode($oldAttributes);
        $log->new_attributes = Json::encode($newAttributes);
        $log->created_at = time();

        $log->save(false);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    //TODO I should replace this code into special DataLogFORM for backend
    public function getEventName()
    {
        return match ($this->event) {
            self::EVENT_INSERT => 'Insert',
            self::EVENT_UPDATE => 'Update',
            self::EVENT_DELETE => 'Delete',
            default => 'Unknown',
        };
    }
    //TODO I should replace this code into special DataLogFORM for backend
    public static function getEventFilters() {
        return [
            self::EVENT_INSERT => 'Insert',
            self::EVENT_UPDATE => 'Update',
            self::EVENT_DELETE => 'Delete',
        ];
    }
}
