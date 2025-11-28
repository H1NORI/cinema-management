<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%programs_user_roles}}`.
 */
class m251128_093210_create_programs_user_roles_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('program_user_roles', [
            'id' => $this->primaryKey(),
            'program_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'role' => "ENUM('PROGRAMMER','STAFF','SUBMITTER') NOT NULL"
        ]);

        $this->createIndex(
            'idx_unique_program_user',
            'program_user_roles',
            ['program_id', 'user_id'],
            true
        );

        $this->addForeignKey(
            'fk_pur_program',
            'program_user_roles',
            'program_id',
            'programs',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_pur_user',
            'program_user_roles',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_pur_user', 'program_user_roles');
        $this->dropForeignKey('fk_pur_program', 'program_user_roles');
        $this->dropTable('program_user_roles');
    }
}
