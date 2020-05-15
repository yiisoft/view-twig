<?php

namespace Yiisoft\Yii\Twig;

use Psr\Container\ContainerInterface;
use Twig\Environment;
use Yiisoft\View\TemplateRendererInterface;
use Yiisoft\View\View;

/**
 * ViewRenderer allows using Twig with a View service
 */
class ViewRenderer implements TemplateRendererInterface
{
    public ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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
