<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_refresh_tokens}}`.
 */
class m251025_142932_create_user_refresh_tokens_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_refresh_tokens}}', [
            'user_refresh_tokenID' => $this->primaryKey(),
            'urf_userID' => $this->integer()->notNull(),
            'urf_token' => $this->string(1000)->notNull(),
            'urf_ip' => $this->string(50)->notNull(),
            'urf_user_agent' => $this->string(1000)->notNull(),
            'urf_created' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_refresh_tokens}}');
    }
}
