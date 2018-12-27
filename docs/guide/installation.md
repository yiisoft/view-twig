Installation
============

Installation consists of two parts: getting composer package and configuring an application.

## Installing an extension

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yiisoft/yii-twig
```

or add

```
"yiisoft/yii-twig": "^3.0"
```

to the require section of your composer.json.

## Configuring application

In order to start using Twig you need to configure the `view` component like the following:

```php
[
    'view' => [
        '__class' => 'yii\web\View',
        'renderers' => [
            'twig' => [
                '__class' => 'yii\twig\ViewRenderer',
                'cachePath' => '@runtime/Twig/cache',
                // Array of twig options:
                'options' => [
                    'auto_reload' => true,
                ],
                'globals' => [
                    'html' => ['class' => '\yii\helpers\Html'],
                ],
                'uses' => ['yii\bootstrap'],
            ],
            // ...
        ],
    ],
]
```

After it's done you can create templates in files that have the `.twig` extension (or use another file extension but
configure the component accordingly). Unlike standard view files, when using Twig you must include the extension
in your `$this->render()` controller call:

```php
return $this->render('renderer.twig', ['username' => 'Alex']);
```
