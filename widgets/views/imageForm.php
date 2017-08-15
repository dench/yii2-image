<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 25.03.17
 * Time: 20:44
 *
 * @var $image dench\image\models\Image
 * @var string $size
 * @var string $modelInputName
 * @var string $fileInputName
 * @var string $label
 * @var string $col
 */

use dench\image\assets\ImageUploadAsset;
use dench\image\widgets\ImageItem;
use kartik\file\FileInput;
use yii\helpers\Url;

ImageUploadAsset::register($this);
?>

<div class="form-group field-page-image">
    <?php if ($label) : ?>
        <label class="control-label" for="page-text"><?= $label ?></label>
    <?php endif; ?>
    <?php
    $initialPreview = [];
    $initialPreviewConfig = [];
    if (!empty($image)) {
        $initialPreview[] = ImageItem::widget([
            'image' => $image,
            'modelInputName' => $modelInputName,
            'size' => $size,
        ]);
        $initialPreviewConfig[] = [
            'url' => Url::to(['/image/ajax/file-hide']),
            'key' => $image->file_id,
        ];
    }
    echo FileInput::widget([
        'id' => $fileInputName,
        'name' => $fileInputName,
        'options' => [
            'multiple' => false,
            'accept' => 'image/jpeg'
        ],
        'language' => Yii::$app->language,
        'pluginOptions' => [
            'initialPreview' => $initialPreview,
            'initialPreviewConfig' => $initialPreviewConfig,
            'overwriteInitial' => true,
            'fileActionSettings' => [
                'showZoom' => false,
                'dragClass' => 'btn btn-xs btn-default',
                'dragSettings' => [
                    'sort' => false,
                ],
            ],
            'previewFileType' => 'image',
            'uploadUrl' => Url::to(['/image/ajax/file-upload']),
            'uploadExtraData' => [
                'modelInputName' => $modelInputName,
                'fileInputName' => $fileInputName,
                'size' => $size,
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
                'actions' => '{delete}',
                'progress' => '',
            ],
            'previewTemplates' => [
                'generic' => '
<div class="file-preview-frame kv-preview-thumb ' . $col . '" id="{previewId}" data-fileindex="{fileindex}" data-template="{template}">
<div class="kv-file-content">
    {content}
</div>
{footer}
</div>',
                'image' => '
<div class="' . $col . '">
<div class="file-preview-frame kv-preview-thumb" id="{previewId}" data-fileindex="{fileindex}" data-template="{template}">
<div class="kv-file-content">
    <img src="{data}" class="kv-preview-data file-preview-image" title="{caption}" alt="{caption}" width="100%">
</div>
{footer}
</div>
</div>',
            ],
        ],
        'pluginEvents' => [
            'filebatchselected' => 'function(event, files) { $("#' . $fileInputName . '").fileinput("upload"); }',
        ],
    ]);
    ?>
</div>
