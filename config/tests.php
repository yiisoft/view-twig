<?php

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\Provider;
use Yiisoft\Factory\Definitions\Reference;
use Yiisoft\Log\Logger;
use Yiisoft\View\WebView;

$tempDir = sys_get_temp_dir();

return [
    ContainerInterface::class => function (ContainerInterface $container) {
        return $container;
    },

    Yiisoft\Aliases\Aliases::class => new Aliases(
        [
            '@root' => dirname(__DIR__, 1),
            '@public' => '@root/tests/public',
            '@basePath' => '@public/assets',
            '@views' => '@public/views',
            '@web' => '/baseUrl'
        ]
    ),
    ListenerProviderInterface::class => [
        '__class' => Provider::class,
    ],

    EventDispatcherInterface::class => [
        '__class' => Dispatcher::class,
        '__construct()' => [
            'listenerProvider' => Reference::to(ListenerProviderInterface::class)
        ],
    ],

    LoggerInterface::class => [
        '__class' => Logger::class,
        '__construct()' => [
            'targets' => [],
        ],
    ],

    //Twig
    \Twig\Environment::class => static function (Psr\Container\ContainerInterface $container) {
        $loader = new \Twig\Loader\FilesystemLoader($container->get(Yiisoft\Aliases\Aliases::class)->get('@views'));

        return new \Twig\Environment(
            $loader,
            [
                //'cache' =>$container->get(Yiisoft\Aliases\Aliases::class)->get('@runtime/cache/twig'),
                'charset' => 'utf-8',
            ]
        );
    },

    //View
    WebView::class => static function (Psr\Container\ContainerInterface $container) {
        $webView = new Yiisoft\View\WebView(
            $container->get(Yiisoft\Aliases\Aliases::class)->get('@views'),
            $container->get(Yiisoft\View\Theme::class),
            $container->get(Psr\EventDispatcher\EventDispatcherInterface::class),
            $container->get(\Psr\Log\LoggerInterface::class)
        );


        $webView->setDefaultExtension('twig');

        /**
         * @var $twig \Twig\Environment
         */
        $twig = $container->get(\Twig\Environment::class);

        $webView->setRenderers(
            [
                'twig' => new \Yiisoft\Yii\Twig\ViewRenderer($twig)
            ]
        );

        $twig->addExtension(new \Yiisoft\Yii\Twig\Extensions\YiiTwigExtension($container));

        return $webView;
    },
];
