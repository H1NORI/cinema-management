<?php

namespace common\models;

use common\components\DataLogBehavior;
use common\exceptions\ApiException;
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
 * @property int $state
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
    const STATE_CREATED = 0;
    const STATE_SUBMISSION = 1;
    const STATE_ASSIGNMENT = 2;
    const STATE_REVIEW = 3;
    const STATE_SCHEDULING = 4;
    const STATE_FINAL_PUBLICATION = 5;
    const STATE_DECISION = 6;
    const STATE_ANNOUNCED = 7;

    public static array $allowedStateTransitions = [
        self::STATE_CREATED => [self::STATE_SUBMISSION],
        self::STATE_SUBMISSION => [self::STATE_ASSIGNMENT],
        self::STATE_ASSIGNMENT => [self::STATE_REVIEW],
        self::STATE_REVIEW => [self::STATE_SCHEDULING],
        self::STATE_SCHEDULING => [self::STATE_FINAL_PUBLICATION],
        self::STATE_FINAL_PUBLICATION => [self::STATE_DECISION],
        self::STATE_DECISION => [self::STATE_ANNOUNCED],
    ];

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
            [['state'], 'default', 'value' => self::STATE_CREATED],
            [['created_by', 'name', 'description', 'start_date', 'end_date', 'created_at', 'updated_at'], 'required'],
            [['created_by', 'created_at', 'updated_at', 'state'], 'integer'],
            [['description'], 'string'],
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
