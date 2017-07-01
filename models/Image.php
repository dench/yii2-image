<?php

namespace dench\image\models;

use dench\image\helpers\ImageHelper;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "image".
 *
 * @property integer $id
 * @property integer $file_id
 * @property string $method
 * @property string $name
 * @property string $alt
 * @property integer $rotate
 * @property integer $mirror
 * @property integer $width
 * @property integer $height
 * @property integer $x
 * @property integer $y
 * @property integer $zoom
 * @property integer $watermark
 *
 * @property File $file
 */
class Image extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
                'slugAttribute' => 'name',
                'ensureUnique' => true,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_id', 'name', 'width', 'height'], 'required'],
            [['file_id', 'rotate', 'mirror', 'width', 'height', 'x', 'y', 'zoom', 'watermark'], 'integer'],
            [['method'], 'string', 'max' => 10],
            [['name', 'alt'], 'string', 'max' => 255],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['file_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file_id' => Yii::t('app', 'File'),
            'method' => Yii::t('app', 'Method'),
            'name' => Yii::t('app', 'Name'),
            'alt' => Yii::t('app', 'Alt'),
            'rotate' => Yii::t('app', 'Rotate'),
            'mirror' => Yii::t('app', 'Mirror'),
            'width' => Yii::t('app', 'Width'),
            'height' => Yii::t('app', 'Height'),
            'x' => Yii::t('app', 'X'),
            'y' => Yii::t('app', 'Y'),
            'zoom' => Yii::t('app', 'Zoom'),
            'watermark' => Yii::t('app', 'Watermark'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(File::className(), ['id' => 'file_id']);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();

        foreach (Yii::$app->params['image']['size'] as $size => $thumb) {
            $path = ImageHelper::generatePath($size);
            $file = Yii::$app->basePath . '/web/' . $path . '/' . $this->name . '.' . $this->file->extension;
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (count($changedAttributes)) {
            foreach (Yii::$app->params['image']['size'] as $size => $thumb) {
                $path = ImageHelper::generatePath($size);
                $file = Yii::$app->basePath . '/web/' . $path . '/' . $this->name . '.' . $this->file->extension;
                if (file_exists($file)) unlink($file);
                if (isset($changedAttributes['name'])) {
                    $file = Yii::$app->basePath . '/web/' . $path . '/' . $changedAttributes['name'] . '.' . $this->file->extension;
                    if (file_exists($file)) unlink($file);
                }
            }
        }
    }

    /**
     * @param Image $model
     * @return bool|string
     */
    public static function resize($model, $size)
    {
        if (empty(Yii::$app->params['image']['size'][$size])) {
            return false;
        }

        $originalFile = Yii::getAlias(Yii::$app->params['file']['path']) . '/' . $model->file->path . '/' . $model->file->hash . '.' . $model->file->extension;

        $param = ArrayHelper::merge(Yii::$app->params['image'], Yii::$app->params['image']['size'][$size]);

        if (isset($param['size'][$size]['watermark'])) {
            $param['watermark'] = ArrayHelper::merge($param['watermark'], $param['size'][$size]['watermark']);
        }

        $param['watermark']['file'] = Yii::getAlias($param['watermark']['file']);

        $newPath = Yii::getAlias('@webroot') . '/' . ImageHelper::generatePath($size);

        $newFile = $newPath . '/' . $model->name . '.' . $model->file->extension;

        $img = \yii\imagine\Image::getImagine()->open($originalFile);

        if (@$param['method'] == 'crop') {
            $k1 = $param['width']/$model->width;
            $k2 = $param['height']/$model->height;
            $k = $k1 > $k2 ? $k1 : $k2;
            $width = round($model->width*$k);
            $height = round($model->height*$k);
            $x = -round(($param['width']-$width)/2);
            $y = -round(($param['height']-$height)/2);
            $img->resize(new Box($width, $height))->crop(new Point($x, $y), new Box($param['width'], $param['height']));
            $width = $param['width'];
            $height = $param['height'];
        } else if (@$param['method'] == 'fill') {
            $k1 = $param['width']/$model->width;
            $k2 = $param['height']/$model->height;
            $k = $k1 < $k2 ? $k1 : $k2;
            $width = round($model->width*$k);
            $height = round($model->height*$k);
            $img->resize(new Box($width, $height));
            $img_new = \yii\imagine\Image::getImagine()->create(new Box($param['width'], $param['height']));
            $x = round(($param['width']-$width)/2);
            $y = round(($param['height']-$height)/2);
            $img_new->paste($img, new Point($x, $y));
            $img = $img_new;
            $width = $param['width'];
            $height = $param['height'];
        } else {
            // force clip
            $k1 = $param['width']/$model->width;
            $k2 = $param['height']/$model->height;
            $k = $k1 < $k2 ? $k1 : $k2;
            $width = round($model->width*$k);
            $height = round($model->height*$k);
            $img->resize(new Box($width, $height));
        }

        $wm = $param['watermark'];
        if ($wm['enabled']) {
            $watermark = \yii\imagine\Image::getImagine()->open($wm['file']);
            $wSize = $watermark->getSize();
            if ($wm['absolute']) {
                $bottomRight = new Point($width - $wSize->getWidth() - $wm['x'], $height - $wSize->getHeight() - $wm['y']);
            } else {
                $bottomRight = new Point($width/(100/$wm['x']) - $wSize->getWidth()/2, $height/(100/$wm['y']) - $wSize->getHeight()/2);
            }
            $img->paste($watermark, $bottomRight);
        }

        FileHelper::createDirectory($newPath);

        if ($img->save($newFile, ['jpeg_quality' => 100])) {
            exec('convert ' . $newFile . ' -sampling-factor 4:2:0 -strip -quality 85 -interlace JPEG -colorspace RGB ' . $newFile);
            return $newFile;
        } else {
            return false;
        }
    }
}
