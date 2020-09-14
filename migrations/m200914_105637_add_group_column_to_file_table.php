<?php

use app\components\Migration;

/**
 * Handles adding columns to table `{{%file}}`.
 */
class m200914_105637_add_group_column_to_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%file}}', 'group', $this->string(32));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%file}}', 'group');
    }
}
