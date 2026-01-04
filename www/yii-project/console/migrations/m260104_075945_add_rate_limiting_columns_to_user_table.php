<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m260104_075945_add_rate_limiting_columns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'allowance', $this->integer()->notNull()->defaultValue(100));
        $this->addColumn('{{%user}}', 'allowance_updated_at', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'allowance');
        $this->dropColumn('{{%user}}', 'allowance_updated_at');
    }
}
