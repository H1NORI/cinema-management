<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%screening_reviews}}`.
 */
class m251128_094842_create_screening_reviews_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('screening_reviews', [
            'id' => $this->primaryKey(),
            'screening_id' => $this->integer()->notNull()->unique(),
            'reviewer_id' => $this->integer()->notNull(),
            'score' => $this->integer()->notNull(),
            'comments' => $this->text()->notNull(),
            'created_at' => $this->integer()->notNull()
        ]);

        $this->addForeignKey(
            'fk_review_screening',
            'screening_reviews',
            'screening_id',
            'screenings',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_review_user',
            'screening_reviews',
            'reviewer_id',
            'user',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_review_user', 'screening_reviews');
        $this->dropForeignKey('fk_review_screening', 'screening_reviews');
        $this->dropTable('screening_reviews');
    }
}
