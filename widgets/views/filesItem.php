<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 28.06.17
 * Time: 11:25
 *
 * @var $file dench\image\models\File
 * @var $modelInputName string
 * @var $key integer
 * @var $enabled boolean
 * @var $name string
 */

use yii\helpers\Html;

?>
<div style="height: 30px;"></div>
<input type="hidden" name="<?= $modelInputName ?>[file_ids][<?= $key ?>]" value="<?= $file->id ?>">
<div class="input-group">
    <?= Html::textInput($modelInputName . '[fileName][' . $key . ']', $name, ['class' => 'form-control input-sm', 'placeholder' => 'Alt name']) ?>
    <span class="input-group-addon">
        <?= Html::checkbox($modelInputName . '[fileEnabled][' . $key . ']', ($enabled) ? true : false, ['value' => $file->id]) ?>
    </span>
</div>
<div class="input-group">
    <?= Html::activeTextInput($file, '[' . $key . ']name', ['class' => 'form-control input-sm']) ?>
    <span class="input-group-addon"><?= $file->extension ?></span>
</div>
