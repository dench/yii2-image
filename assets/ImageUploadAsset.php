<?php

namespace dench\image\assets;

use yii\web\AssetBundle;

class ImageUploadAsset extends AssetBundle
{
    public $sourcePath = '@vendor/dench/yii2-image/assets';
    public $css = [
        'css/image-upload.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'kartik\file\FileInputAsset',
        'kartik\base\WidgetAsset',
    ];
}
