<?php

namespace dench\image\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "file".
 *
 * @property integer $id
 * @property string $path
 * @property string $hash
 * @property string $extension
 * @property string $type
 * @property integer $size
 * @property string $name
 * @property boolean $enabled
 * @property integer $created_at
 *
 * @property Image[] $images
 */
class File extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['path', 'hash', 'extension', 'type', 'size'], 'required'],
            [['size'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['hash', 'type'], 'string', 'max' => 32],
            [['extension', 'path'], 'string', 'max' => 10],
            [['enabled'], 'boolean'],
            [['enabled'], 'default', 'value' => true],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'path' => Yii::t('app', 'Path'),
            'hash' => Yii::t('app', 'Hash'),
            'extension' => Yii::t('app', 'Extension'),
            'type' => Yii::t('app', 'Type'),
            'size' => Yii::t('app', 'Size'),
            'name' => Yii::t('app', 'Name'),
            'enabled' => Yii::t('app', 'Enabled'),
            'created_at' => Yii::t('app', 'Created'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(Image::className(), ['file_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        foreach ($this->images as $image) {
            $image->delete();
        }

        return parent::beforeDelete();
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();

        $dub = File::findOne([
            'path' => $this->path,
            'hash' => $this->hash,
            'extension' => $this->extension,
        ]);

        $file = Yii::$app->params['filePath'] . '/' . $this->path . '/' . $this->id . '.' . $this->extension;

        if (empty($dub) && file_exists($file)) {
            unlink($file);
        }
    }
}
