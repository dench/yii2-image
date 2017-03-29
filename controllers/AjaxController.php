<?php

namespace dench\image\controllers;

use dench\image\helpers\ImageHelper;
use dench\image\models\UploadFiles;
use Yii;
use yii\helpers\Html;
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

            $name = Yii::$app->request->post('name');
            $size = Yii::$app->request->post('size') ? Yii::$app->request->post('size') : 'small';

            $model = new UploadFiles();
            $model->files = UploadedFile::getInstancesByName('files');

            if ($model->upload()) {
                $initialPreview = [];
                $initialPreviewConfig = [];
                foreach ($model->upload as $key => $upload) {
                    $html = '<img src="' . ImageHelper::thumb($upload['image']->id, $size) . '" alt="" width="100%"><input type="hidden" name="' . $name . '[' . $upload['image']->id . ']" value="' . $upload['image']->id . '">';
                    $html .= Html::activeTextInput($upload['image'], '[' . $upload['image']->id . ']alt', ['class' => 'form-control input-sm', 'placeholder' => 'Alt']);
                    $html .= '<div class="input-group">';
                    $html .= Html::activeTextInput($upload['image'], '[' . $upload['image']->id . ']name', ['class' => 'form-control input-sm']);
                    $html .= '<span class="input-group-addon">.' . $upload['file']->extension . '</span>';
                    $html .= '</div>';
                    $initialPreview[] = $html;
                    $initialPreviewConfig[] = [
                        'url' => Url::to(['/admin/ajax/file-hide']),
                        'key' => $upload['file']->id,
                    ];
                }
                return [
                    'initialPreview' => $initialPreview,
                    'initialPreviewConfig' => $initialPreviewConfig,
                ];
            }

            return [
                'error' => $model->errors['files'],
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