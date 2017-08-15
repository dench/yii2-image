<?php
/**
 * Created by PhpStorm.
 * User: Dench
 * Date: 28.01.2017
 * Time: 22:40
 */

namespace dench\image\models;

use DateTime;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class UploadFile extends Model
{
    /**
     * @var UploadedFile
     */
    public $file;

    public $upload;

    private $extensions;
    private $maxSize;
    private $path;

    public function init()
    {
        parent::init();

        $param = Yii::$app->params['file'];

        $this->extensions = $param['extensions'];
        $this->maxSize = $param['maxSize'];
        $this->path = $param['path'];
    }

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => $this->extensions, 'maxSize' => $this->maxSize],
        ];
    }

    public function upload()
    {
        $this->upload = [];

        if ($this->validate()) {

            $date = new DateTime();
            $path = $date->format('Y/m/d');

            FileHelper::createDirectory($this->path . '/' .$path);

            $type = $this->file->type;
            $size = $this->file->size;
            $extension = $this->file->extension;
            $hash = md5_file($this->file->tempName);

            $dub = File::findOne([
                'hash' => $hash,
                'size' => $size,
                'type' => $type,
                'extension' => $extension,
            ]);

            if (empty($dub)) {
                $f = new File();
                $f->hash = $hash;
                $f->type = $type;
                $f->size = $size;
                $f->extension = $extension;
                $f->path = $path;
                if ($f->save()) {
                    $this->file->saveAs($this->path . '/' .$path . '/' . $f->hash . '.' . $f->extension);
                }
            } else {
                $f = $dub;
            }

            $this->upload['file'] = $f;

            if (preg_match('#^image/#', $f->type)) {

                $dub = Image::findOne(['file_id' => $f->id]);

                if (empty($dub)) {
                    $image = new Image();
                    $image->file_id = $f->id;
                    $image->name = str_replace('_', '-', $this->file->baseName);
                    $img = \yii\imagine\Image::getImagine()->open($this->path . '/' . $f->path . '/' . $f->hash . '.' . $f->extension);
                    $image->width = $img->getSize()->getWidth();
                    $image->height = $img->getSize()->getHeight();
                    $image->save();
                } else {
                    $image = $dub;
                }

                $this->upload['image'] = $image;
            }

            return $this->upload;
        } else {
            return false;
        }
    }
}