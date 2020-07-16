<?php

namespace dench\image\models;

use dench\image\helpers\ImageHelper;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
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
        return '{{%image}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'slug' => [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'name',
                'ensureUnique' => true,
                'skipOnEmpty' => true,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_id', 'width', 'height'], 'required'],
            [['file_id', 'rotate', 'mirror', 'width', 'height', 'x', 'y', 'zoom', 'watermark'], 'integer'],
            [['method'], 'string', 'max' => 10],
            [['name', 'alt'], 'string', 'max' => 255],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['file_id' => 'id']],
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
        return $this->hasOne(File::class, ['id' => 'file_id']);
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
    public static function resize($model, $size, $filename = null, $path = null)
    {
        if (empty(Yii::$app->params['image']['size'][$size])) {
            return false;
        }

        $originalFile = Yii::getAlias(Yii::$app->params['file']['path']) . '/' . $model->file->path . '/' . $model->file->hash . '.' . $model->file->extension;

        if (!file_exists($originalFile)) {
            return false;
        }

        $param = ArrayHelper::merge(Yii::$app->params['image'], Yii::$app->params['image']['size'][$size]);

        if (isset($param['size'][$size]['watermark'])) {
            $param['watermark'] = ArrayHelper::merge($param['watermark'], $param['size'][$size]['watermark']);
        }

        $param['watermark']['file'] = Yii::getAlias($param['watermark']['file']);

        $newPath = $path ? $path : Yii::getAlias('@webroot') . '/' . ImageHelper::generatePath($size);

        $filename = $filename ? $filename : $model->name . '.' . $model->file->extension;

        $newFile = $newPath . '/' . $filename;

        $img = \yii\imagine\Image::getImagine()->open($originalFile);

        $method = $model->method ? $model->method : @$param['method'];

        if (in_array($model->rotate, [90, 270])) {
            $img->rotate($model->rotate);
            $temp = $model->width;
            $model->width = $model->height;
            $model->height = $temp;
        } elseif ($model->rotate === 180) {
            $img->rotate($model->rotate);
        }

        $palette = new RGB();
        $color = $palette->color(\yii\imagine\Image::$thumbnailBackgroundColor, \yii\imagine\Image::$thumbnailBackgroundAlpha);

        if ($param['width'] > $model->width && $param['height'] > $model->height) {
            $p = $param['width'] / $param['height'];
            if ($model->width >= $model->height) {
                $param['width'] = $model->width;
                $param['height'] = $param['width'] / $p;
            } else {
                $param['height'] = $model->height;
                $param['width'] = $param['height'] / $p;
            }
        }

        $k1 = $param['width'] / $model->width;
        $k2 = $param['height'] / $model->height;

        if ($method === 'crop') {
            $k = $k1 > $k2 ? $k1 : $k2;
            $width = round($model->width * $k);
            $height = round($model->height * $k);
            $x = -round(($param['width'] - $width) / 2);
            $y = -round(($param['height'] - $height) / 2);

            $img->resize(new Box($width, $height))->crop(new Point($x, $y), new Box($param['width'], $param['height']));
            $img_new = \yii\imagine\Image::getImagine()->create(new Box($param['width'], $param['height']), $color);
            $img_new->paste($img, new Point(0, 0));
            $img = $img_new;
            $width = $param['width'];
            $height = $param['height'];
        } else if ($method === 'fill') {
            $k = $k1 < $k2 ? $k1 : $k2;
            $width = round($model->width * $k);
            $height = round($model->height * $k);
            $x = round(($param['width'] - $width) / 2);
            $y = round(($param['height'] - $height) / 2);

            $img->resize(new Box($width, $height));
            $img_new = \yii\imagine\Image::getImagine()->create(new Box($param['width'], $param['height']), $color);
            $img_new->paste($img, new Point($x, $y));
            $img = $img_new;
            $width = $param['width'];
            $height = $param['height'];
        } else {
            // clip
            $k = $k1 < $k2 ? $k1 : $k2;
            $width = round($model->width * $k);
            $height = round($model->height * $k);
            $img->resize(new Box($width, $height));
            $img_new = \yii\imagine\Image::getImagine()->create(new Box($width, $height), $color);
            $img_new->paste($img, new Point(0, 0));
            $img = $img_new;
        }

        $wm = $param['watermark'];
        if ($wm['enabled']) {
            $watermark = \yii\imagine\Image::getImagine()->open($wm['file']);
            $wSize = $watermark->getSize();
            $wSizeW = $wSize->getWidth();
            $wSizeH = $wSize->getHeight();
            $wSizeP = $wSizeW / $wSizeH;
            if ($wSizeH > $height) {
                $wSizeW = $height / $wSizeP;
                $wSizeH = $height;
                $watermark->resize(new Box($wSizeW, $wSizeH));
            } elseif (!empty($wm['width'])) {
                $wSizeW = $wm['width'];
                $wSizeH = $wm['width'] / $wSizeP;
                $watermark->resize(new Box($wSizeW, $wSizeH));
            }
            if ($wm['absolute']) {
                $w = $width - $wSizeW - $wm['x'];
                $h = $height - $wSizeH - $wm['y'];
            } else {
                $w = $width / (100 / $wm['x']) - ($wSizeW / 2);
                $h = $height / (100 / $wm['y']) - ($wSizeH / 2);
            }
            if ($w > 0 && $h > 0) {
                $img->paste($watermark, new Point($w, $h));
            }
        }

        FileHelper::createDirectory($newPath);

        if ($img->save($newFile, ['jpeg_quality' => Yii::$app->params['image']['jpeg_quality']])) {
            if (Yii::$app->params['image']['convert']) {
                exec('convert ' . $newFile . ' -sampling-factor 4:2:0 -strip -quality 85 -interlace JPEG -colorspace RGB ' . $newFile);
            }
            return $newFile;
        }

        return false;
    }
}
