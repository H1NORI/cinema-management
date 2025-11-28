<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%screening_decisions}}`.
 */
class m251128_095139_create_screening_decisions_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('screening_decisions', [
            'id' => $this->primaryKey(),
            'screening_id' => $this->integer()->notNull()->unique(),
            'decision' => "ENUM('APPROVED','REJECTED') NOT NULL",
            'reason' => $this->text(),
            'decided_by' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk_decision_screening',
            'screening_decisions',
            'screening_id',
            'screenings',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_decision_user',
            'screening_decisions',
            'decided_by',
            'user',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_decision_user', 'screening_decisions');
        $this->dropForeignKey('fk_decision_screening', 'screening_decisions');
        $this->dropTable('screening_decisions');
    }
}
