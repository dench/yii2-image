<?php

namespace dench\image\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

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
 * @property integer $user_id
 *
 * @property string $downloadKey
 * @property string $downloadName
 * @property string $fullDownloadName
 * @property string $fullPath
 *
 * @property Image[] $images
 * @property Image $image
 */
class File extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
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
            [['size', 'user_id'], 'integer'],
            [['name', 'type'], 'string', 'max' => 255],
            [['hash'], 'string', 'max' => 32],
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
            'user_id' => Yii::t('app', 'User'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery|Image[]
     */
    public function getImages()
    {
        return $this->hasMany(Image::class, ['file_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery|Image
     */
    public function getImage()
    {
        return $this->hasOne(Image::class, ['file_id' => 'id'])->orderBy(['id' => SORT_DESC]);
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

        $duplicate = self::findDuplicate($this->hash, $this->size);

        $file = Yii::getAlias(Yii::$app->params['file']['path']) . '/' . $this->path . '/' . $this->hash . '.' . $this->extension;

        if ($duplicate === null && file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * @param $hash
     * @param null $size
     * @return File[]|null
     */
    public static function findDuplicates($hash, $size = null)
    {
        $return = null;

        if ($temp = self::find()->where(['hash' => $hash])->all()) {
            foreach ($temp as $t) {
                if (!$size || $t->size === $size) {
                    $return[] = $t;
                }
            }
        }

        return $return;
    }

    /**
     * @param $hash
     * @param null $size
     * @return File|null
     */
    public static function findDuplicate($hash, $size = null)
    {
        if ($ds = self::findDuplicates($hash, $size)) {
            return current($ds);
        }

        return null;
    }

    public function isImage(): bool
    {
        return 0 === strpos($this->type, 'image');
    }

    public function getDownloadKey(): string
    {
        return substr(md5($this->id . Yii::$app->params['file']['downloadSecurityKey']), 10, 5);
    }

    public function getDownloadName(): string
    {
        return Inflector::slug($this->name);
    }

    public function getFullDownloadName(): string
    {
        return $this->id . '-' . $this->downloadName . '.' . $this->extension;
    }

    public function getFullPath(): string
    {
        return Yii::$app->params['file']['path'] . '/' . $this->path . '/' . $this->hash . '.' . $this->extension;
    }
}
