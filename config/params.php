<?php

return [
    'file' => [
        'extensions' => 'png, jpg',
        'maxSize' => 25*1024*1024,
        'maxFiles' => 50,
        'path' => dirname(__DIR__) . '/files',
    ],
    'image' => [
        'path' => 'image',
        'jpeg_quality' => 100,
        'convert' => true,
        'watermark' => [
            'enabled' => true,
            'absolute' => false,
            'file' => '@webroot/img/watermark.png',
            'x' => 50,
            'y' => 50,
        ],
        'none' => '/img/none.png',
        'size' => [
            'normal' => [
                'width' => 800,
                'height' => 600,
                'method' => 'clip',
            ],
            'cover' => [
                'width' => 400,
                'height' => 400,
                'method' => 'crop',
                'watermark' => [
                    'enabled' => false,
                ],
            ],
        ],
    ],
];
