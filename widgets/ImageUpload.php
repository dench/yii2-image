<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 25.03.17
 * Time: 13:27
 */

namespace dench\image\widgets;

use yii\base\Widget;

class ImageUpload extends Widget
{
    public $images;

    public $size = 'cover';

    public $fileInputName = 'files';

    public $modelInputName;

    public function run()
    {
        return $this->render('imageUpload', [
            'images' => $this->images,
            'size' => $this->size,
            'fileInputName' => $this->fileInputName,
            'modelInputName' => $this->modelInputName,
        ]);
    }
}