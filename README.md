<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <a href="https://twig.symfony.com/" target="_blank">
        <img src="https://twig.symfony.com/images/twig-logo.png" height="100px">
    </a>
    <h1 align="center">Yii View Twig Renderer</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/view-twig/v/stable.png)](https://packagist.org/packages/yiisoft/view-twig)
[![Total Downloads](https://poser.pugx.org/yiisoft/view-twig/downloads.png)](https://packagist.org/packages/yiisoft/view-twig)
[![Build Status](https://github.com/yiisoft/view-twig/workflows/build/badge.svg)](https://github.com/yiisoft/view-twig/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/view-twig/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/view-twig/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/view-twig/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/view-twig/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fview-twig%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/view-twig/master)
[![static analysis](https://github.com/yiisoft/view-twig/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/view-twig/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/view-twig/coverage.svg)](https://shepherd.dev/github/yiisoft/view-twig)

The package is an extension of the [Yii View Rendering Library](https://github.com/yiisoft/view/). This extension
provides a `ViewRender` that would allow you to use [Twig](https://twig.symfony.com/) view template engine.

## Requirements

- PHP 8.0 or higher.

## Installation

The package could be installed with composer:

```
composer require yiisoft/view-twig --prefer-dist
```

## General usage

In your application, you should specify the configuration for `Twig`
(by default, this is `config/packages/yiisoft/view-twig/common.php`):

```php
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Yiisoft\Aliases\Aliases;
use Yiisoft\View\Twig\Extensions\YiiTwigExtension;
   
return [
    Environment::class => static function (ContainerInterface $container): Environment {
        $loader = new FilesystemLoader($container
            ->get(Aliases::class)
            ->get('@views'));

        $twig = new Environment($loader, [
            'cache' => $container
                ->get(Aliases::class)
                ->get('@runtime/cache/twig'),
            'charset' => 'utf-8',
        ]);

        $twig->addExtension(new YiiTwigExtension($container));
        return $twig;
    },
];
```

And also override the configuration for `WebView` (by default, this is `config/packages/yiisoft/view/web.php`):

```php
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Yiisoft\Aliases\Aliases;
use Yiisoft\View\WebView;
use Yiisoft\View\Twig\ViewRenderer;

/** @var array $params */
   
return [
    //...
    WebView::class => static function (ContainerInterface $container) use ($params): WebView {
        $webView = new WebView(
            $container
                ->get(Aliases::class)
                ->get('@views'),
            $container->get(EventDispatcherInterface::class),
        );

        $webView = $webView
            ->withDefaultExtension('twig')
            ->withRenderers(['twig' => new ViewRenderer($container->get(Environment::class))])
        ;

        $webView->setCommonParameters($params['yiisoft/view']['commonParameters']);
        return $webView;
    },
    //...
];
```

### Template

All variables that were in the regular template are also available in the twig template.

The `get(string $id);` function allows you to get the definition that was set by the container,
this function is available in all view templates and layouts:

```twig
{{ get('App\\Widget\\PerformanceMetrics').widget()|raw }}
```

The default main layout of the [application template](https://github.com/yiisoft/app) will look like this:

```twig
{{ assetManager.register(['App\\Asset\\AppAsset', 'App\\Asset\\CdnFontAwesomeAsset']) }}
{{ this.addCssFiles(assetManager.getCssFiles()) }}
{{ this.addCssStrings(assetManager.getCssStrings()) }}
{{ this.addJsFiles(assetManager.getJsFiles()) }}
{{ this.addJsStrings(assetManager.getJsStrings()) }}
{{ this.addJsVars(assetManager.getJsVars()) }}
{{ this.beginPage()|raw }}
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ this.getTitle() }}</title>
        {{ this.head()|raw }}
    </head>
    <body>
    {{ this.beginBody()|raw }}
        <section class="hero is-fullheight is-light">
            <div class="hero-head has-background-black">
                {{ get('Yiisoft\\Yii\\Bulma\\NavBar').widget()
                    .brandLabel(applicationParameters.getName())
                    .brandImage('/images/yii-logo.jpg')
                    .options({'class': 'is-black', 'data-sticky': '', 'data-sticky-shadow': ''})
                    .itemsOptions({'class': 'navbar-end'})
                    .begin()|raw
                }}
                {{ get('Yiisoft\\Yii\\Bulma\\Nav').widget()
                    .currentPath(urlMatcher.getCurrentUri() != null ? urlMatcher.getCurrentUri().getPath() : '')
                    .items([])|raw
                }}
                {{ get('Yiisoft\\Yii\\Bulma\\NavBar').end()|raw }}
            </div>
            <div class="hero-body is-light">
                <div class="container has-text-centered">
                    {{ content|raw }}
                </div>
            </div>
            <div class="hero-footer has-background-black">
                <div class="columns is-mobile">
                    <div class="column has-text-left has-text-light">
                        <i class="fas fa-copyright fa-inverse is-hidden-mobile"></i>
                        <a class="is-hidden-mobile" href="https://www.yiiframework.com/" target="_blank" rel="noopener">
                            {{ 'now'|date('Y') }} {{ applicationParameters.getName() }}
                        </a>
                        <a class="is-hidden-desktop is-size-6" href="https://www.yiiframework.com/" target="_blank" rel="noopener">
                            {{ applicationParameters.getName() }}
                        </a>
                    </div>
                    <div class="column has-text-centered has-text-light is-hidden-mobile"></div>
                    <div class="column has-text-right has-text-light">
                        <span class="icon">
                            <a href="https://github.com/yiisoft" target="_blank" rel="noopener">
                                <i class="fab fa-github fa-inverse" aria-hidden="true"></i>
                            </a>
                        </span>
                        <span class="icon">
                            <a href="https://join.slack.com/t/yii/shared_invite/enQtMzQ4MDExMDcyNTk2LTc0NDQ2ZTZhNjkzZDgwYjE4YjZlNGQxZjFmZDBjZTU3NjViMDE4ZTMxNDRkZjVlNmM1ZTA1ODVmZGUwY2U3NDA" target="_blank" rel="noopener">
                                <i class="fab fa-slack fa-inverse " aria-hidden="true"></i>
                            </a>
                        </span>
                        <span class="icon">
                            <a href="https://www.facebook.com/groups/yiitalk" target="_blank" rel="noopener">
                                <i class="fab fa-facebook-f fa-inverse" aria-hidden="true"></i>
                            </a>
                        </span>
                        <span class="icon">
                            <a href="https://twitter.com/yiiframework" target="_blank" rel="noopener">
                                <i class="fab fa-twitter fa-inverse" aria-hidden="true"></i>
                            </a>
                        </span>
                        <span class="icon">
                            <a href="https://t.me/yii3ru" target="_blank" rel="noopener">
                                <i class="fab fa-telegram-plane fa-inverse"></i>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </section>
    {{ this.endBody()|raw }}
    </body>
</html>
{{ this.endPage(true)|raw }}
```

And the view template of the main page (`site/index`) will be as follows:

```twig
{% do this.setTitle(applicationParameters.getName()) %}

<h1 class="title">Hello!</h1>

<p class="subtitle">Let's start something great with <strong>Yii3</strong>!</p>

<p class="subtitle is-italic">
    <a href="https://github.com/yiisoft/docs/tree/master/guide/en" target="_blank" rel="noopener">
        Don't forget to check the guide.
    </a>
</p>
```

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework with
[Infection Static Analysis Plugin](https://github.com/Roave/infection-static-analysis-plugin). To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

## License

The Yii Framework Twig Extension is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
