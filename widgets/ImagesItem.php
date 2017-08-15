<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 28.06.17
 * Time: 11:30
 */

namespace dench\image\widgets;

use yii\base\Widget;

class ImagesItem extends Widget
{
    /** @var $image dench\image\models\Image */
    public $image;

    public $modelInputName;

    public $size;

    public $key;

    public $cover = 0;

    public $enabled = 1;

    public function run()
    {
        return $this->render('imagesItem', [
            'image' => $this->image,
            'modelInputName' => $this->modelInputName,
            'size' => $this->size,
            'key' => $this->key,
            'cover' => $this->cover,
            'enabled' => $this->enabled,
        ]);
    }
}