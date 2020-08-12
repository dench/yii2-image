<?php
/**
 * Created by PhpStorm.
 * User: Dench
 * Date: 12.08.2020
 * Time: 12:50
 */

namespace dench\image\models;

use Yii;

class UploadImage extends UploadFile
{
    public $maxWidth;
    public $maxHeight;
    public $minWidth;
    public $minHeight;

    public function init()
    {
        parent::init();
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['file'],
                'image',
                'skipOnEmpty' => $this->skipOnEmpty,
                'extensions' => $this->extensions,
                'maxSize' => $this->maxSize,
                'maxWidth' => $this->maxWidth,
                'maxHeight' => $this->maxHeight,
                'minWidth' => $this->minWidth,
                'minHeight' => $this->minHeight,
            ],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'file' => Yii::t('app', 'Загрузить изображение'),
        ];
    }
}
