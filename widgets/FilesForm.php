<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 25.03.17
 * Time: 13:27
 */

namespace dench\image\widgets;

use yii\base\Widget;

class FilesForm extends Widget
{
    /** @var $files dench\image\models\File[] */
    public $files;

    public $fileInputName = 'files';

    public $modelInputName = 'Page';

    public $col = 'col-sm-4';

    public $label = 'Files';

    public $fileEnabled = [];

    public $fileName = [];

    public function run()
    {
        return $this->render('filesForm', [
            'files' => $this->files,
            'fileInputName' => $this->fileInputName,
            'modelInputName' => $this->modelInputName,
            'col' => $this->col,
            'label' => $this->label,
            'fileEnabled' => $this->fileEnabled,
            'fileName' => $this->fileName,
        ]);
    }
}