<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Twig;

use Psr\Container\ContainerInterface;
use Twig\Environment;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\View\TemplateRendererInterface;
use Yiisoft\View\View;
use Yiisoft\Yii\Web\User\User;

/**
 * Class ViewRenderer
 * @package Yiisoft\Yii\Twig
 *
 * @author Javharbek Abdulatipov <jakharbek@gmail.com>
 */
class ViewRenderer implements TemplateRendererInterface
{
    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * ViewRenderer constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    /**
     * @param View $view
     * @param string $template
     * @param array $params
     * @return string
     * @throws \Throwable
     */
    public function render(View $view, string $template, array $params): string
    {
        $container = $this->container;
        $renderer = function () use ($view, $template, $params,$container) {
            $file = str_replace($view->getBasePath(), null, $template);
            echo $container->get(Environment::class)->render($file, array_merge([
                'this' => $view
            ], $params));
        };

        $obInitialLevel = ob_get_level();
        ob_start();
        ob_implicit_flush(0);
        try {
            $renderer->bindTo($view)($template, $params);
            return ob_get_clean();
        } catch (\Throwable $e) {
            while (ob_get_level() > $obInitialLevel) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }
}
