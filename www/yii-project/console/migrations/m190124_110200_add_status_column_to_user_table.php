<?php

use \yii\db\Migration;

class m190124_110200_add_status_column_to_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}','status', $this->smallInteger()->notNull()->defaultValue(10));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'status');
    }
}
