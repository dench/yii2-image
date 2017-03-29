<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 25.03.17
 * Time: 20:44
 *
 * @var array $images
 * @var string $size
 * @var string $modelInputName
 * @var string $fileInputName
 */

use dench\image\assets\ImageUploadAsset;
use dench\image\helpers\ImageHelper;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;


ImageUploadAsset::register($this);
?>

<div class="form-group field-page-image">
    <label class="control-label" for="page-text">Images</label>
    <?php
    $initialPreview = [];
    $initialPreviewConfig = [];
    foreach ($images as $key => $image) {
        $html = '<img src="' . ImageHelper::thumb($image->id, $size) . '" alt="" width="100%"><input type="hidden" name="' . $modelInputName . '[' . $key . ']" value="' . $image->id . '">';
        $html .= Html::activeTextInput($image, '[' . $key . ']alt', ['class' => 'form-control input-sm', 'placeholder' => 'Alt']);
        $html .= '<div class="input-group">';
        $html .= Html::activeTextInput($image, '[' . $key . ']name', ['class' => 'form-control input-sm']);
        $html .= '<span class="input-group-addon">.' . $image->file->extension . '</span>';
        $html .= '</div>';
        $initialPreview[] = $html;
        $initialPreviewConfig[] = [
            'url' => Url::to(['/image/ajax/file-hide']),
            'key' => $image->file_id,
        ];
    }
    echo FileInput::widget([
        'id' => $fileInputName,
        'name' => $fileInputName . '[]',
        'options' => [
            'multiple' => true,
            'accept' => 'image/jpeg'
        ],
        'language' => Yii::$app->language,
        'pluginOptions' => [
            'initialPreview' => $initialPreview,
            'initialPreviewConfig' => $initialPreviewConfig,
            'fileActionSettings' => [
                'showZoom' => false,
                'dragClass' => 'btn btn-xs btn-default',
                'dragSettings' => [
                    'sort' => true,
                    'draggable' => '.file-sortable',
                    'handle' => '.file-move',
                ],
            ],
            'previewFileType' => 'image',
            'uploadUrl' => Url::to(['/image/ajax/file-upload']),
            'uploadExtraData' => [
                'name' => $modelInputName,
                'size' => $size,
            ],
            'uploadAsync' => false,
            'showUpload' => false,
            'showRemove' => false,
            'showBrowse' => true,
            'showCaption' => false,
            'showClose' => false,
            'showPreview ' => false,
            'dropZoneEnabled' => false,
            'layoutTemplates' => [
                'modalMain' => '',
                'modal' => '',
                'footer' => '<div class="file-thumbnail-footer">{actions}</div>',
                'actions' => '{delete}<button class="file-move btn btn-xs btn-default"><i class="glyphicon glyphicon-move"></i></button>',
                'progress' => '',
            ],
            'previewTemplates' => [
                'generic' => '
<div class="file-preview-frame kv-preview-thumb drag-handle-init col-sm-4 file-sortable" id="{previewId}" data-fileindex="{fileindex}" data-template="{template}">
<div class="kv-file-content">
    {content}
</div>
{footer}
</div>',
                'image' => '
<div class="col-sm-4">
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
