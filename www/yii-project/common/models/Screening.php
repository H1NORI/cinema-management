<?php

namespace common\models;

use common\components\DataLogBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "screenings".
 *
 * @property int $id
 * @property int $program_id
 * @property int $state
 * @property string|null $film_title
 * @property string|null $film_cast
 * @property string|null $film_genres
 * @property int|null $film_duration
 * @property string|null $auditorium
 * @property string|null $start_time
 * @property string|null $end_time
 * @property string|null $rejection_reason
 * @property int|null $score
 * @property string|null $comments
 * @property int $submitter_id
 * @property int|null $handler_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $handler
 * @property Program $program
 * @property ScreeningDecision $screeningDecisions
 * @property ScreeningReview $screeningReviews
 * @property User $submitter
 */
class Screening extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATE_CREATED = 0;
    const STATE_SUBMITTED = 1;
    const STATE_REVIEWED = 2;
    const STATE_APPROVED = 3;
    const STATE_FINALLY_SUBMITTED = 4;
    const STATE_SCHEDULED = 5;
    const STATE_REJECTED = 6;


    public static array $allowedStateTransitions = [
        self::STATE_CREATED => [self::STATE_SUBMITTED],
        self::STATE_SUBMITTED => [self::STATE_REVIEWED],
        self::STATE_REVIEWED => [self::STATE_APPROVED, self::STATE_REJECTED],
        self::STATE_APPROVED => [self::STATE_FINALLY_SUBMITTED],
        self::STATE_FINALLY_SUBMITTED => [self::STATE_SCHEDULED],
        self::STATE_SCHEDULED => [self::STATE_REJECTED],
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'screenings';
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
            [['film_title', 'film_cast', 'film_genres', 'film_duration', 'auditorium', 'start_time', 'end_time', 'rejection_reason', 'handler_id'], 'default', 'value' => null],
            [['state'], 'default', 'value' => self::STATE_CREATED],
            [['program_id', 'submitter_id', 'created_at', 'updated_at'], 'required'],
            [['program_id', 'film_duration', 'submitter_id', 'handler_id', 'created_at', 'updated_at'], 'integer'],
            [['film_cast', 'rejection_reason'], 'string'],
            [['start_time', 'end_time'], 'safe'],
            [['film_title', 'film_genres', 'auditorium'], 'string', 'max' => 255],
            ['state', 'in', 'range' => array_keys(self::optsState())],
            [['handler_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['handler_id' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_id' => 'id']],
            [['submitter_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['submitter_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_id' => 'Program ID',
            'state' => 'State',
            'film_title' => 'Film Title',
            'film_cast' => 'Film Cast',
            'film_genres' => 'Film Genres',
            'film_duration' => 'Film Duration',
            'auditorium' => 'Auditorium',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'submitter_id' => 'Submitter ID',
            'handler_id' => 'Handler ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Handler]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHandler()
    {
        return $this->hasOne(User::class, ['id' => 'handler_id']);
    }

    /**
     * Gets query for [[Program]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::class, ['id' => 'program_id']);
    }

    /**
     * Gets query for [[ScreeningDecisions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScreeningDecisions()
    {
        return $this->hasOne(ScreeningDecision::class, ['screening_id' => 'id']);
    }

    /**
     * Gets query for [[ScreeningReviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScreeningReviews()
    {
        return $this->hasOne(ScreeningReview::class, ['screening_id' => 'id']);
    }

    /**
     * Gets query for [[Submitter]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubmitter()
    {
        return $this->hasOne(User::class, ['id' => 'submitter_id']);
    }


    /**
     * column state ENUM value labels
     * @return string[]
     */
    public static function optsState()
    {
        return [
            self::STATE_CREATED => 'CREATED',
            self::STATE_SUBMITTED => 'SUBMITTED',
            self::STATE_REVIEWED => 'REVIEWED',
            self::STATE_APPROVED => 'APPROVED',
            self::STATE_FINALLY_SUBMITTED => 'FINALLY SUBMITTED',
            self::STATE_SCHEDULED => 'SCHEDULED',
            self::STATE_REJECTED => 'REJECTED',
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
    public function isStateSubmitted()
    {
        return $this->state === self::STATE_SUBMITTED;
    }

    public function setStateToSubmitted()
    {
        $this->state = self::STATE_SUBMITTED;
    }

    /**
     * @return bool
     */
    public function isStateReviewed()
    {
        return $this->state === self::STATE_REVIEWED;
    }

    public function setStateToReviewed()
    {
        $this->state = self::STATE_REVIEWED;
    }

    /**
     * @return bool
     */
    public function isStateApproved()
    {
        return $this->state === self::STATE_APPROVED;
    }

    public function setStateToApproved()
    {
        $this->state = self::STATE_APPROVED;
    }

    /**
     * @return bool
     */
    public function isStateFinallySubmitted()
    {
        return $this->state === self::STATE_FINALLY_SUBMITTED;
    }

    public function setStateToFinallySubmitted()
    {
        $this->state = self::STATE_FINALLY_SUBMITTED;
    }

    /**
     * @return bool
     */
    public function isStateScheduled()
    {
        return $this->state === self::STATE_SCHEDULED;
    }

    public function setStateToScheduled()
    {
        $this->state = self::STATE_SCHEDULED;
    }

    /**
     * @return bool
     */
    public function isStateRejected()
    {
        return $this->state === self::STATE_REJECTED;
    }

    public function setStateToRejected()
    {
        $this->state = self::STATE_REJECTED;
    }
}
