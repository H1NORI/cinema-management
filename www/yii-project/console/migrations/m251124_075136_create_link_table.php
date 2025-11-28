<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%link}}`.
 */
class m251124_075136_create_link_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%link}}', [
            'id' => $this->primaryKey(),
            'label' => $this->string()->notNull(),
            'is_header' => $this->boolean()->defaultValue(false),

            'url' => $this->string(),
            'priority' => $this->integer()->notNull()->defaultValue(0),
            'icon' => $this->string(),
            'icon_style' => $this->string(),
            'target' => $this->string(),

            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%link}}');
    }
}
