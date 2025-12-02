<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%screenings}}`.
 */
class m251128_093256_create_screenings_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('screenings', [
            'id' => $this->primaryKey(),
            'program_id' => $this->integer()->notNull(),

            'state' => $this->tinyInteger()->notNull(),

            // film info
            'film_title' => $this->string(),
            'film_cast' => $this->text(),
            'film_genres' => $this->string(),
            'film_duration' => $this->integer(),

            // auditorium
            'auditorium' => $this->string(),

            // times
            'start_time' => $this->dateTime(),
            'end_time' => $this->dateTime(),

            'rejection_reason' => $this->text(),

            // roles
            'submitter_id' => $this->integer()->notNull(),
            'handler_id' => $this->integer(),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk_screenings_program',
            'screenings',
            'program_id',
            'programs',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_screenings_submitter',
            'screenings',
            'submitter_id',
            'user',
            'id'
        );

        $this->addForeignKey(
            'fk_screenings_handler',
            'screenings',
            'handler_id',
            'user',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_screenings_handler', 'screenings');
        $this->dropForeignKey('fk_screenings_submitter', 'screenings');
        $this->dropForeignKey('fk_screenings_program', 'screenings');
        $this->dropTable('screenings');
    }
}
