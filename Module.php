<?php

namespace dench\image;

use Yii;

/**
 * Class Module
 *
 * @package dench\image
 */
class Module extends \yii\base\Module
{
    /**
     * @var string the namespace that controller classes are in
     */
    public $controllerNamespace = 'dench\image\controllers';

    public function init()
    {
        parent::init();

        Yii::$app->i18n->translations['page'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath' => '@dench/image/messages',
        ];
    }
}