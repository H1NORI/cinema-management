<?php

namespace common\models;

use common\components\DataLogBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "programs".
 *
 * @property int $id
 * @property int $created_by
 * @property string $name
 * @property string $description
 * @property string $start_date
 * @property string $end_date
 * @property string $state
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $createdBy
 * @property ProgramUserRole[] $programUserRoles
 * @property Screening[] $screenings
 * @property User[] $users
 */
class Program extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATE_CREATED = 'CREATED';
    const STATE_SUBMISSION = 'SUBMISSION';
    const STATE_ASSIGNMENT = 'ASSIGNMENT';
    const STATE_REVIEW = 'REVIEW';
    const STATE_SCHEDULING = 'SCHEDULING';
    const STATE_FINAL_PUBLICATION = 'FINAL_PUBLICATION';
    const STATE_DECISION = 'DECISION';
    const STATE_ANNOUNCED = 'ANNOUNCED';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'programs';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            DataLogBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['state'], 'default', 'value' => 'CREATED'],
            [['created_by', 'name', 'description', 'start_date', 'end_date', 'created_at', 'updated_at'], 'required'],
            [['created_by', 'created_at', 'updated_at'], 'integer'],
            [['description', 'state'], 'string'],
            [['start_date', 'end_date'], 'safe'],
            [['name'], 'string', 'max' => 255],
            ['state', 'in', 'range' => array_keys(self::optsState())],
            [['name'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_by' => 'Created By',
            'name' => 'Name',
            'description' => 'Description',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'state' => 'State',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[ProgramUserRoles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgramUserRoles()
    {
        return $this->hasMany(ProgramUserRole::class, ['program_id' => 'id']);
    }

    /**
     * Gets query for [[Screenings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScreenings()
    {
        return $this->hasMany(Screening::class, ['program_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('program_user_roles', ['program_id' => 'id']);
    }


    /**
     * column state ENUM value labels
     * @return string[]
     */
    public static function optsState()
    {
        return [
            self::STATE_CREATED => 'CREATED',
            self::STATE_SUBMISSION => 'SUBMISSION',
            self::STATE_ASSIGNMENT => 'ASSIGNMENT',
            self::STATE_REVIEW => 'REVIEW',
            self::STATE_SCHEDULING => 'SCHEDULING',
            self::STATE_FINAL_PUBLICATION => 'FINAL_PUBLICATION',
            self::STATE_DECISION => 'DECISION',
            self::STATE_ANNOUNCED => 'ANNOUNCED',
        ];
    }

    /**
     * @return string
     */
    public function displayState()
    {
        return self::optsState()[$this->state];
    }

    /**
     * @return bool
     */
    public function isStateCreated()
    {
        return $this->state === self::STATE_CREATED;
    }

    public function setStateToCreated()
    {
        $this->state = self::STATE_CREATED;
    }

    /**
     * @return bool
     */
    public function isStateSubmission()
    {
        return $this->state === self::STATE_SUBMISSION;
    }

    public function setStateToSubmission()
    {
        $this->state = self::STATE_SUBMISSION;
    }

    /**
     * @return bool
     */
    public function isStateAssignment()
    {
        return $this->state === self::STATE_ASSIGNMENT;
    }

    public function setStateToAssignment()
    {
        $this->state = self::STATE_ASSIGNMENT;
    }

    /**
     * @return bool
     */
    public function isStateReview()
    {
        return $this->state === self::STATE_REVIEW;
    }

    public function setStateToReview()
    {
        $this->state = self::STATE_REVIEW;
    }

    /**
     * @return bool
     */
    public function isStateScheduling()
    {
        return $this->state === self::STATE_SCHEDULING;
    }

    public function setStateToScheduling()
    {
        $this->state = self::STATE_SCHEDULING;
    }

    /**
     * @return bool
     */
    public function isStateFinalpublication()
    {
        return $this->state === self::STATE_FINAL_PUBLICATION;
    }

    public function setStateToFinalpublication()
    {
        $this->state = self::STATE_FINAL_PUBLICATION;
    }

    /**
     * @return bool
     */
    public function isStateDecision()
    {
        return $this->state === self::STATE_DECISION;
    }

    public function setStateToDecision()
    {
        $this->state = self::STATE_DECISION;
    }

    /**
     * @return bool
     */
    public function isStateAnnounced()
    {
        return $this->state === self::STATE_ANNOUNCED;
    }

    public function setStateToAnnounced()
    {
        $this->state = self::STATE_ANNOUNCED;
    }
}
