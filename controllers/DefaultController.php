<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 11.03.17
 * Time: 23:41
 */

namespace dench\image\controllers;

use dench\image\models\File;
use dench\image\models\Image;
use Yii;
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
            readfile($file);
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

    public function actionFile($name, $extension)
    {
        $model = File::findOne([
            'name' => $name,
            'extension' => $extension,
            'enabled' => true,
        ]);

        if ($model !== null) {
            $file = Yii::getAlias(Yii::$app->params['file']['path']) . '/' . $model->path . '/' . $model->hash . '.' . $model->extension;
            if (file_exists($file)) {
                header('Content-Type: ' . $model->type);
                readfile($file);
            } else {
                throw new NotFoundHttpException('The requested file does not exist.');
            }
        } else {
            throw new NotFoundHttpException('The requested file does not exist.');
        }
        die();
    }
}
