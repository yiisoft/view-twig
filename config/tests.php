<?php

$_ENV['TEST_RUNTIME_PATH'] = $_ENV['TEST_RUNTIME_PATH'] ?? dirname(__DIR__) . '/runtime';

return [
    'app' => [
        'id' => 'testapp',
        'aliases' => [
            '@webroot'           => '@yii/twig/tests/assets',
            '@runtime'           => $_ENV['TEST_RUNTIME_PATH'],
            '@app'               => '@yii/twig/tests/',
            '@app/views'         => '@yii/twig/tests/views',
            '@app/modules'       => '@yii/twig/tests/views',
            '@app/widgets'       => '@yii/twig/tests/views',
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
