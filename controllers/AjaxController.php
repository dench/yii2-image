<?php

namespace dench\image\controllers;

use dench\image\models\UploadFile;
use dench\image\models\UploadFiles;
use dench\image\widgets\FilesItem;
use dench\image\widgets\ImageItem;
use dench\image\widgets\ImagesItem;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class AjaxController extends Controller
{
    public function actionFilesUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax) {

            $modelInputName = Yii::$app->request->post('modelInputName');
            $fileInputName = Yii::$app->request->post('fileInputName');

            $model = new UploadFiles();
            $model->files = UploadedFile::getInstancesByName($fileInputName);

            if ($model->upload()) {
                $initialPreview = [];
                $initialPreviewConfig = [];
                foreach ($model->upload as $key => $upload) {
                    $initialPreview[] = FilesItem::widget([
                        'file' => $upload['file'],
                        'modelInputName' => $modelInputName,
                        'key' => $upload['file']->id,
                        'enabled' => 1,
                        'name' => $upload['file']->name,
                    ]);
                    $initialPreviewConfig[] = [
                        'url' => Url::to(['/image/ajax/file-hide']),
                        'key' => $upload['file']->id,
                    ];
                }
                return [
                    'initialPreview' => $initialPreview,
                    'initialPreviewConfig' => $initialPreviewConfig,
                ];
            }

            return [
                'error' => current($model->errors),
            ];
        }
        return [
            'error' => 'Error!',
        ];
    }

    public function actionFileHide()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [];
    }

    public function actionImagesUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax) {

            $modelInputName = Yii::$app->request->post('modelInputName');
            $fileInputName = Yii::$app->request->post('fileInputName');
            $size = Yii::$app->request->post('size') ? Yii::$app->request->post('size') : 'small';

            $model = new UploadFiles();
            $model->extensions = Yii::$app->params['image']['extensions'];
            $model->files = UploadedFile::getInstancesByName($fileInputName);

            if ($model->upload()) {
                $initialPreview = [];
                $initialPreviewConfig = [];
                foreach ($model->upload as $key => $upload) {
                    $initialPreview[] = ImagesItem::widget([
                        'image' => $upload['image'],
                        'modelInputName' => $modelInputName,
                        'size' => $size,
                        'key' => $upload['image']->id,
                        'enabled' => 1,
                    ]);
                    $initialPreviewConfig[] = [
                        'url' => Url::to(['/image/ajax/image-hide']),
                        'key' => $upload['file']->id,
                    ];
                }
                return [
                    'initialPreview' => $initialPreview,
                    'initialPreviewConfig' => $initialPreviewConfig,
                ];
            }

            return [
                'error' => current($model->errors),
            ];
        }
        return [
            'error' => 'Error!',
        ];
    }

    public function actionImageUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax) {

            $modelInputName = Yii::$app->request->post('modelInputName');
            $fileInputName = Yii::$app->request->post('fileInputName');
            $size = Yii::$app->request->post('size') ? Yii::$app->request->post('size') : 'small';

            $model = new UploadFile();
            $model->extensions = Yii::$app->params['image']['extensions'];
            $model->file = UploadedFile::getInstanceByName($fileInputName);

            if ($model->upload()) {
                $initialPreview = [];
                $initialPreviewConfig = [];
                if (!empty($model->upload)) {
                    $initialPreview[] = ImageItem::widget([
                        'image' => $model->upload['image'],
                        'modelInputName' => $modelInputName,
                        'size' => $size,
                    ]);
                    $initialPreviewConfig[] = [
                        'url' => Url::to(['/image/ajax/image-hide']),
                        'key' => $model->upload['file']->id,
                    ];
                }
                return [
                    'initialPreview' => $initialPreview,
                    'initialPreviewConfig' => $initialPreviewConfig,
                ];
            }

            return [
                'error' => current($model->error),
            ];
        }
        return [
            'error' => 'Error!',
        ];
    }

    public function actionImageHide()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [];
    }
}