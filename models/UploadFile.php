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

    public $extensions;

    public $fromUrl = false;

    public $skipOnEmpty = false;

    public $maxSize;

    private $path;

    public function init()
    {
        parent::init();

        $param = Yii::$app->params['file'];

        $this->extensions = ($this->extensions) ? $this->extensions : $param['extensions'];

        $this->maxSize = $param['maxSize'];
        $this->path = Yii::getAlias($param['path']);
    }

    public function rules()
    {
        if ($this->fromUrl) {
            $rules = [['file'], 'igogo5yo\uploadfromurl\FileFromUrlValidator', 'extensions' => $this->extensions, 'maxSize' => $this->maxSize];
        } else {
            $rules = [['file'], 'file', 'skipOnEmpty' => $this->skipOnEmpty, 'extensions' => $this->extensions, 'maxSize' => $this->maxSize];
        }

        return [$rules];
    }

    public function attributeLabels()
    {
        return [
            'file' => Yii::t('app', 'Загрузить файл'),
        ];
    }

    public function upload()
    {
        $this->upload = [];

        if ($this->validate() && $this->file) {

            $date = new DateTime();
            $path = $date->format('Y/m/d');

            FileHelper::createDirectory($this->path . '/' .$path);

            $type = $this->file->type;
            $size = $this->file->size;
            $extension = $this->file->extension;
            if ($this->fromUrl) {
                $fileContents = file_get_contents($this->file->url);
                $hash = md5($fileContents);
            } else {
                $hash = md5_file($this->file->tempName);
            }

            $dub = File::findDuplicate($hash, $size);

            $f = new File();
            $f->hash = $hash;
            $f->type = $type;
            $f->size = $size;
            $f->extension = $dub ? $dub->extension : $extension;
            $f->path = $dub ? $dub->path : $path;
            if ($this->fromUrl) {
                preg_match('/.*\/(.+?)\..+?$/', $this->file->url, $out);
                $baseName = @$out[1];
            } else {
                $baseName = $this->file->baseName;
            }
            $f->name = str_replace('_', '-', $baseName);
            $f->user_id = Yii::$app->user->getId();
            if ($f->save() && empty($dub)) {
                $this->file->saveAs($this->path . '/' .$path . '/' . $f->hash . '.' . $f->extension);
            }

            $this->upload['file'] = $f;

            if (preg_match('#^image/#', $f->type)) {
                $image = new Image();
                $image->file_id = $f->id;
                $image->name = $f->name;
                $img = \yii\imagine\Image::getImagine()->open($this->path . '/' . $f->path . '/' . $f->hash . '.' . $f->extension);
                $image->width = $img->getSize()->getWidth();
                $image->height = $img->getSize()->getHeight();
                $image->save();

                $this->upload['image'] = $image;
            }

            return $this->upload;
        }

        return false;
    }
}
