<?php

use yii\db\Migration;

/**
 * Handles the creation of table `image`.
 */
class m170310_104136_create_image_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('image', [
            'id' => $this->primaryKey(),
            'file_id' => $this->integer()->notNull(),
            'method' => $this->string(10),
            'name' => $this->string()->notNull(),
            'alt' => $this->string(),
            'rotate' => $this->smallInteger(),
            'mirror' => $this->boolean()->defaultValue(false),
            'width' => $this->integer()->notNull(),
            'height' => $this->integer()->notNull(),
            'x' => $this->integer(),
            'y' => $this->integer(),
            'zoom' => $this->smallInteger(3),
            'watermark' => $this->boolean(),
        ]);

        $this->addForeignKey('fk-image-file_id', 'image', 'file_id', 'file', 'id', 'CASCADE');
    }
    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-image-file_id', 'image');

        $this->dropTable('image');
    }
}
