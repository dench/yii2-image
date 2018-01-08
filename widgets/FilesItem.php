<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 28.06.17
 * Time: 11:30
 */

namespace dench\image\widgets;

use yii\base\Widget;

class FilesItem extends Widget
{
    /** @var $file dench\image\models\File */
    public $file;

    public $modelInputName;

    public $key;

    public $enabled = 1;

    public $name;

    public function run()
    {
        return $this->render('filesItem', [
            'file' => $this->file,
            'modelInputName' => $this->modelInputName,
            'key' => $this->key,
            'enabled' => $this->enabled,
            'name' => $this->name,
        ]);
    }
}