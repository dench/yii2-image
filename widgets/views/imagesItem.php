<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 28.06.17
 * Time: 11:25
 *
 * @var $image dench\image\models\Image
 * @var $size string
 * @var $modelInputName string
 * @var $key integer
 * @var $cover boolean
 * @var $enabled boolean
 */

use dench\image\helpers\ImageHelper;
use yii\helpers\Html;

?>
<img src="<?= ImageHelper::thumb($image->id, $size) ?>" alt="" width="100%"><input type="hidden" name="<?= $modelInputName ?>[image_ids][<?= $key ?>]" value="<?= $image->id ?>">
<div class="input-group">
    <?= Html::activeTextInput($image, '[' . $key . ']alt', ['class' => 'form-control input-sm', 'placeholder' => 'Alt']) ?>
    <span class="input-group-addon">
        <?= Html::radio($modelInputName . '[image_id]', ($cover) ? true : false, ['value' => $image->id]) ?>
        <?= Html::checkbox($modelInputName . '[imageEnabled][' . $key . ']', ($enabled) ? true : false, ['value' => $image->id]) ?>
    </span>
</div>
<div class="input-group">
    <?= Html::activeTextInput($image, '[' . $key . ']name', ['class' => 'form-control input-sm']) ?>
    <span class="input-group-addon"><?= $image->file->extension ?></span>
</div>
