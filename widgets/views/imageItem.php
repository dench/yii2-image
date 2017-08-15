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
 */

use dench\image\helpers\ImageHelper;
use yii\helpers\Html;

?>
<img src="<?= ImageHelper::thumb($image->id, $size) ?>" alt="" width="100%"><input type="hidden" name="<?= $modelInputName ?>[image_id]" value="<?= $image->id ?>">
<?= Html::activeTextInput($image, 'alt', ['class' => 'form-control input-sm', 'placeholder' => 'Alt']) ?>
<div class="input-group">
    <?= Html::activeTextInput($image, 'name', ['class' => 'form-control input-sm']) ?>
    <span class="input-group-addon"><?= $image->file->extension ?></span>
</div>
