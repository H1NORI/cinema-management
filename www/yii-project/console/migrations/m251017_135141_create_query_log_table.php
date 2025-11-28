<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%query_log}}`.
 */
class m251017_135141_create_query_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%query_log}}', [
            'id' => $this->primaryKey(),
            'route' => $this->string(),
            'query' => $this->text(),
            'new_attributes' => $this->text(),
            'execution_time' => $this->float()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%query_log}}');
    }
}
