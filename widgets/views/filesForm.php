<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 25.03.17
 * Time: 20:44
 *
 * @var $files dench\image\models\File[]
 * @var string $modelInputName
 * @var string $fileInputName
 * @var string $label
 * @var string $col
 * @var array $fileEnabled
 * @var array $fileName
 */

use dench\image\assets\ImageUploadAsset;
use dench\image\widgets\FilesItem;
use kartik\file\FileInput;
use yii\helpers\Url;


ImageUploadAsset::register($this);
?>

<div class="form-group field-page-file">
    <?php if ($label) : ?>
        <label class="control-label" for="page-text"><?= $label ?></label>
    <?php endif; ?>
    <?php
    $initialPreview = [];
    $initialPreviewConfig = [];
    foreach ($files as $key => $file) {
        $initialPreview[] = FilesItem::widget([
            'file' => $file,
            'modelInputName' => $modelInputName,
            'key' => $file->id,
            'enabled' => @$fileEnabled[$file->id],
            'name' => ($fileName[$file->id]) ? $fileName[$file->id] : $file->name,
        ]);
        $initialPreviewConfig[] = [
            'url' => Url::to(['/image/ajax/file-hide']),
            'key' => $file->id,
        ];
    }
    echo FileInput::widget([
        'id' => $fileInputName,
        'name' => $fileInputName . '[]',
        'options' => [
            'multiple' => true,
        ],
        'language' => Yii::$app->language,
        'pluginOptions' => [
            'initialPreview' => $initialPreview,
            'initialPreviewConfig' => $initialPreviewConfig,
            'overwriteInitial' => false,
            'fileActionSettings' => [
                'showZoom' => false,
                'dragClass' => 'btn btn-xs btn-default',
                'dragSettings' => [
                    'sort' => true,
                    'draggable' => '.file-sortable',
                    'handle' => '.file-move',
                ],
            ],
            'previewFileType' => 'file',
            'uploadUrl' => Url::to(['/image/ajax/files-upload']),
            'uploadExtraData' => [
                'modelInputName' => $modelInputName,
                'fileInputName' => $fileInputName,
            ],
            'uploadAsync' => false,
            'showUpload' => false,
            'showRemove' => false,
            'showBrowse' => true,
            'showCaption' => false,
            'showClose' => false,
            'showPreview' => true,
            'dropZoneEnabled' => false,
            'layoutTemplates' => [
                'modalMain' => '',
                'modal' => '',
                'footer' => '<div class="file-thumbnail-footer">{actions}</div>',
                'actions' => '{delete}<button type="button" class="file-move btn btn-xs btn-default"><i class="glyphicon glyphicon-move"></i></button>',
                'progress' => '',
            ],
            'previewTemplates' => [
                'generic' => '
<div class="file-preview-frame kv-preview-thumb drag-handle-init file-sortable ' . $col . '" id="{previewId}" data-fileindex="{fileindex}" data-template="{template}">
<div class="kv-file-content">
    {content}
</div>
{footer}
</div>',
            ],
        ],
        'pluginEvents' => [
            'filebatchselected' => 'function(event, files) { $("#' . $fileInputName . '").fileinput("upload"); }',
        ],
    ]);
    ?>
</div>
