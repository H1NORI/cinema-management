<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "screening_reviews".
 *
 * @property int $id
 * @property int $screening_id
 * @property int $reviewer_id
 * @property int $score
 * @property string $comments
 * @property int $created_at
 *
 * @property User $reviewer
 * @property Screening $screening
 */
class ScreeningReview extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'screening_reviews';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['screening_id', 'reviewer_id', 'score', 'comments', 'created_at'], 'required'],
            [['screening_id', 'reviewer_id', 'score', 'created_at'], 'integer'],
            [['comments'], 'string'],
            [['screening_id'], 'unique'],
            [['screening_id'], 'exist', 'skipOnError' => true, 'targetClass' => Screening::class, 'targetAttribute' => ['screening_id' => 'id']],
            [['reviewer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['reviewer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'screening_id' => 'Screening ID',
            'reviewer_id' => 'Reviewer ID',
            'score' => 'Score',
            'comments' => 'Comments',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Reviewer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviewer()
    {
        return $this->hasOne(User::class, ['id' => 'reviewer_id']);
    }

    /**
     * Gets query for [[Screening]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScreening()
    {
        return $this->hasOne(Screening::class, ['id' => 'screening_id']);
    }

}
