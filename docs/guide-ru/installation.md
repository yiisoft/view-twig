Установка
============

Установка состоит из двух частей: получение пакета расширения через Composer и конфигурирование приложения.

## Установка расширения

Предпочтительный способ установки расширения через [Composer](http://getcomposer.org/download/).

Для этого запустите команду

```
php composer.phar require --prefer-dist yiisoft/yii-twig
```

или добавьте

```
"yiisoft/yii-twig": "^3.0.0"
```

в секцию require вашего composer.json.

## Конфигурирование приложения

Чтобы использовать шаблонизатор Twig, вам необходимо сконфигурировать компонент `view` следующим образом:

```php
[
    'view' => [
        '__class' => 'yii\web\View',
        'renderers' => [
            'twig' => [
                '__class' => 'Yiisoft\Yii\Twig\ViewRenderer',
                'cachePath' => '@runtime/Twig/cache',
                // Array of twig options:
                'options' => [
                    'auto_reload' => true,
                ],
                'globals' => [
                    'html' => ['__class' => '\yii\helpers\Html'],
                ],
                'uses' => ['yii\bootstrap'],
            ],
            // ...
        ],
    ],
]
```

После этого вы можете создавать шаблоны в файлах с расширением `.twig` (или использовать другое расширение файла, 
предварительно переконфигурировав компонент). В отличие от стандартных файлов представления, при использовании шаблонизатора 
Twig вы должны указывать расширение в вызове метода контроллера `$this->render()`:

```php
return $this->render('renderer.twig', ['username' => 'Alex']);
```
