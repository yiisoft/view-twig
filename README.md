<p align="center">
    <a href="https://twig.symfony.com/" target="_blank">
        <img src="https://twig.symfony.com/images/twig-logo.png" height="200px">
    </a>
    <h1 align="center">Yii Framework Twig Extension</h1>
    <br>
</p>

This extension provides a `ViewRender` that would allow you to use [Twig](http://twig.sensiolabs.org/) view template engine
with [Yii framework](http://www.yiiframework.com).

For license information check the [LICENSE](LICENSE.md)-file.

Documentation is at [docs/guide/README.md](docs/guide/README.md).

[![Latest Stable Version](https://poser.pugx.org/yiisoft/yii-twig/v/stable.png)](https://packagist.org/packages/yiisoft/yii-twig)
[![Total Downloads](https://poser.pugx.org/yiisoft/yii-twig/downloads.png)](https://packagist.org/packages/yiisoft/yii-twig)
[![Build Status](https://github.com/yiisoft/yii-twig/workflows/build/badge.svg)](https://github.com/yiisoft/yii-twig/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/yii-twig/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/yii-twig/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/yii-twig/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/yii-twig/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fyii-twig%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/yii-twig/master)
[![static analysis](https://github.com/yiisoft/yii-twig/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/yii-twig/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/yii-twig/coverage.svg)](https://shepherd.dev/github/yiisoft/yii-twig)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

```
php composer.phar require --prefer-dist yiisoft/yii-twig
```

## Usage

You should specify `twig` and `view` in the configuration:

```php
//Twig    
return [
    //...
    //Twig
    \Twig\Environment::class => static function (\Psr\Container\ContainerInterface $container) {
        $loader = new \Twig\Loader\FilesystemLoader($container->get(\Yiisoft\Aliases\Aliases::class)->get('@views'));

        return new \Twig\Environment($loader, array_merge([
            'cache' =>$container->get(Yiisoft\Aliases\Aliases::class)->get('@runtime/cache/twig'),
            'charset' => 'utf-8',
        ], []));
    }, 
    //View
    WebView::class => static function (\Psr\Container\ContainerInterface $container) {
        $webView = new \Yiisoft\View\WebView(
            $container->get(\Yiisoft\Aliases\Aliases::class)->get('@views'),
            $container->get(\Yiisoft\View\Theme::class),
            $container->get(\Psr\EventDispatcher\EventDispatcherInterface::class),
            $container->get(\Psr\Log\LoggerInterface::class)
        );

        $webView = $webView
            ->withDefaultParameters([
                'assetManager' => $container->get(\Yiisoft\Assets\AssetManager::class),
                'urlGenerator' => $container->get(\Yiisoft\Router\UrlGeneratorInterface::class),
            ])
            ->withDefaultExtension('twig')
            ->withRenderers([
                'twig' => new \Yiisoft\Yii\Twig\ViewRenderer($container)
            ]);

        $container->get(\Twig\Environment::class)->addExtension(new \Yiisoft\Yii\Twig\Extensions\YiiTwigExtension($container));

        return $webView;
    },
    //...
];
``` 

## Template

All variables that were in the regular template are also available in the twig template.

* `get(string id)` this is a function of accessing the container, in addition, there is a global variable throughout the templates `container`

```twig
{{ get('App\\Widget\\PerformanceMetrics').widget()|raw }}
```

## Example

main.twig

```twig
{{ assetManager.register(['App\\Asset\\AppAsset']) }}
{{ this.setCssFiles(assetManager.getCssFiles()) }}
{{ this.setJsFiles(assetManager.getJsFiles()) }}
{{ this.beginPage()|raw }}

<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yii Demo (Twig)</title>
    {{ this.head()|raw }}
</head>
    <body>
    {{ this.beginBody()|raw }}

        {{
        get('Yiisoft\\Yii\\Bootstrap4\\NavBar').begin()
        .brandLabel('Yii Demo')
        .brandUrl(urlGenerator.generate('site/index'))
        .options({'class' : 'navbar navbar-light bg-light navbar-expand-sm text-white'})
        .start()|raw
        }}

        {{
        get('Yiisoft\\Yii\\Bootstrap4\\Nav').widget()
        .currentPath(currentUrl)
        .options({'class' : 'navbar-nav mr-auto'})
        .items(
            [
                {'label' : 'Blog', 'url' : urlGenerator.generate('blog/index')},
                {'label' : 'Comments Feed', 'url' : urlGenerator.generate('blog/comment/index')},
                {'label' : 'Users', 'url' : urlGenerator.generate('user/index')},
                {'label' : 'Contact', 'url' : urlGenerator.generate('site/contact')},
            ]
        )|raw
        }}
        
        {{
        get('Yiisoft\\Yii\\Bootstrap4\\Nav').widget()
        .currentPath(currentUrl)
        .options({'class' : 'navbar-nav'})
        .items(
            user.getId() == null ?
            [
                {'label' : 'Login', 'url' : urlGenerator.generate('site/login')},
                {'label' : 'Signup', 'url' : urlGenerator.generate('site/signup')},
            ]
            :
            [
                {'label' : "Logout (" ~ user.getLogin() ~ ")", 'url' : urlGenerator.generate('site/logout')},
            ]
        )|raw
        }}

        {{ get('Yiisoft\\Yii\\Bootstrap4\\NavBar').end()|raw }}

        <main role="main" class="container py-4">
            {{ content|raw }}
        </main>
        <footer class="container py-4">
            {{ get('App\\Widget\\PerformanceMetrics').widget()|raw }}
        </footer>

    {{ this.endBody()|raw }}
    </body>
</html>
{{ this.endPage(true)|raw }}
```

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework. To run it:

```shell
./vendor/bin/infection
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

### Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

### Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)

## License

The Yii Framework Twig Extension is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).
