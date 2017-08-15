<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 25.03.17
 * Time: 13:27
 */

namespace dench\image\widgets;

use yii\base\Widget;

class ImageForm extends Widget
{
    /** @var $image dench\image\models\Image */
    public $image;

    public $size = 'cover';

    public $fileInputName = 'file';

    public $modelInputName = 'Page';

    public $col = 'col-sm-4';

    public $label = 'Image';

    public function run()
    {
        return $this->render('imageForm', [
            'image' => $this->image,
            'size' => $this->size,
            'fileInputName' => $this->fileInputName,
            'modelInputName' => $this->modelInputName,
            'col' => $this->col,
            'label' => $this->label,
        ]);
    }
}