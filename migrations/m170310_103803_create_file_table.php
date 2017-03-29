<?php

use yii\db\Migration;

/**
 * Handles the creation of table `file`.
 */
class m170310_103803_create_file_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('file', [
            'id' => $this->primaryKey(),
            'path' => $this->string(10)->notNull(),
            'hash' => $this->string(32)->notNull(),
            'extension' => $this->string(10)->notNull(),
            'type' => $this->string(32)->notNull(),
            'size' => $this->integer()->notNull(),
            'name' => $this->string(),
            'enabled' => $this->boolean()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
        ]);
    }
    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('file');
    }
}
