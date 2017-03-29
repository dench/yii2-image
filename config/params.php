<?php

return [
    'file' => [
        'extensions' => 'png, jpg',
        'maxSize' => 10*1024*1024,
        'maxFiles' => 50,
        'path' => dirname(__DIR__) . '/files',
    ],
    'image' => [
        'path' => 'image',
        'jpeg_quality' => 90,
        'watermark' => [
            'enabled' => 1,
            'file' => '@webroot/img/watermark.png',
            'x' => 40,
            'y' => 30
        ],
        'none' => '/img/photo-default.png',
        'size' => [
            'big' => [
                'width' => 1024,
                'height' => 768,
            ],
            'small' => [
                'width' => 600,
                'height' => 600,
                'watermark' => [
                    'file' => '@webroot/img/watermark-small.png',
                ],
            ],
            'cover' => [
                'width' => 600,
                'height' => 600,
                'method' => 'fill',
                'bg' => '#FFFFFF',
            ],
        ],
    ],
];
