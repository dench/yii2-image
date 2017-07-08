<?php
/**
 * Created by PhpStorm.
 * User: Dench
 * Date: 29.01.2017
 * Time: 20:20
 */

namespace dench\image\helpers;

use dench\image\models\Image;
use Yii;
use yii\web\NotFoundHttpException;

class ImageHelper
{
    /**
     * @param integer $id
     * @param string $size = big|small|cover|...
     * @return string
     */
    public static function thumb($id, $size)
    {
        return static::generateUrl($id, $size);
    }

    /**
     * @param string $size = big|small|cover|...
     * @return string
     */
    public static function generatePath($size)
    {
        $param = Yii::$app->params['image'];

        if (empty($param['size'][$size])) {
            return false;
        }

        $thumb = $param['size'][$size];

        $dir = isset($thumb['dir']) ? $thumb['dir'] : $size;

        return $param['path'] . '/' . $dir;
    }

    public static function generateHash($model)
    {
        return substr(md5(
            $model->file->hash .
            $model->method .
            $model->rotate .
            $model->mirror .
            $model->x .
            $model->y .
            $model->zoom
        ), 0, 6);
    }

    public static function generateName($id)
    {
        $model = static::findModel($id);

        return $model->name . '.' . $model->file->extension . '?i=' . static::generateHash($model);
    }

    /**
     * @param integer $id
     * @param string $size = big|small|cover|...
     * @return string
     */
    protected static function generateUrl($id, $size)
    {
        $path = static::generatePath($size);

        return '/' . $path . '/' . static::generateName($id);
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Image the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected static function findModel($id)
    {
        if (($model = Image::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested image does not exist.');
        }
    }
}