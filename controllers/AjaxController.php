<?php

namespace dench\image\controllers;

use dench\image\models\UploadFiles;
use dench\image\widgets\ImageItem;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class AjaxController extends Controller
{
    public function actionFileUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax) {

            $modelInputName = Yii::$app->request->post('modelInputName');
            $fileInputName = Yii::$app->request->post('fileInputName');
            $size = Yii::$app->request->post('size') ? Yii::$app->request->post('size') : 'small';

            $model = new UploadFiles();
            $model->files = UploadedFile::getInstancesByName($fileInputName);

            if ($model->upload()) {
                $initialPreview = [];
                $initialPreviewConfig = [];
                foreach ($model->upload as $key => $upload) {
                    $initialPreview[] = ImageItem::widget([
                        'image' => $upload['image'],
                        'modelInputName' => $modelInputName,
                        'size' => $size,
                        'key' => $upload['image']->id,
                        'enabled' => 1,
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
                'error' => $model->errors[$fileInputName],
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
}