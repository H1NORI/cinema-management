<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%programs}}`.
 */
class m251128_093141_create_programs_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('programs', [
            'id' => $this->primaryKey(),
            'created_by' => $this->integer()->notNull(),
            'name' => $this->string()->notNull()->unique(),
            'description' => $this->text()->notNull(),

            'start_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),

            'state' => "ENUM(
                'CREATED',
                'SUBMISSION',
                'ASSIGNMENT',
                'REVIEW',
                'SCHEDULING',
                'FINAL_PUBLICATION',
                'DECISION',
                'ANNOUNCED'
            ) NOT NULL DEFAULT 'CREATED'",

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk_programs_created_by',
            'programs',
            'created_by',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_programs_created_by', 'programs');
        $this->dropTable('programs');
    }
}
