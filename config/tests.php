<?php

$_ENV['TEST_RUNTIME_PATH'] = $_ENV['TEST_RUNTIME_PATH'] ?? dirname(__DIR__) . '/runtime';

return [
    'app' => [
        'id' => 'testapp',
        'aliases' => [
            '@webroot'           => '@Yiisoft/Yii/Twig/Tests/assets',
            '@runtime'           => $_ENV['TEST_RUNTIME_PATH'],
            '@app'               => '@Yiisoft/Yii/Twig/Tests/',
            '@app/views'         => '@Yiisoft/Yii/Twig/Tests/views',
            '@app/modules'       => '@Yiisoft/Yii/Twig/Tests/views',
            '@app/widgets'       => '@Yiisoft/Yii/Twig/Tests/views',
            '@yii/tests/runtime' => $_ENV['TEST_RUNTIME_PATH'],
        ],
    ],
    'cache' => [
        '__class' => \yii\cache\FileCache::class,
    ],
    'assetManager' => [
        '__class'   => yii\web\AssetManager::class,
        'basePath'  => '@webroot',
        'baseUrl'   => '@web/assets',
    ]
];
