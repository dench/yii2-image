<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 11.03.17
 * Time: 23:41
 */

namespace dench\image\controllers;

use dench\image\models\Image;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * @param $name
     * @param $size = big|small|cover|...
     * @throws NotFoundHttpException
     */
    public function actionIndex($name, $size)
    {
        $model = $this->findModel($name);

        if ($file = Image::resize($model, $size)) {
            header('Content-Type: ' . $model->file->type);
            print file_get_contents($file);
        } else {
            throw new NotFoundHttpException('Image not found!');
        }
        die();
    }

    /**
     * Finds the Page model based on its name value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $name
     * @return Image the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($name)
    {
        if (($model = Image::findOne(['name' => $name])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested image does not exist.');
        }
    }

}