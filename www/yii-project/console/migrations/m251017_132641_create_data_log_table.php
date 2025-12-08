<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%data_log}}`.
 */
class m251017_132641_create_data_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%data_log}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'model_id' => $this->integer()->null(),
            'model' => $this->string(),
            'event' => $this->tinyInteger()->notNull(),
            'old_attributes' => $this->text(),
            'new_attributes' => $this->text(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-data_log-user_id',
            'data_log',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%data_log}}');
    }
}
