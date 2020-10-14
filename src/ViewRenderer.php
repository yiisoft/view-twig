<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Twig;

use Twig\Environment;
use Yiisoft\View\TemplateRendererInterface;
use Yiisoft\View\View;

/**
 * ViewRenderer allows using Twig with a View service
 */
class ViewRenderer implements TemplateRendererInterface
{
    private Environment $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function render(View $view, string $template, array $params): string
    {
        $environment = $this->environment;
        $renderer = function () use ($view, $template, $params, $environment) {
            $file = str_replace($view->getBasePath(), '', $template);
            echo $environment->render($file, array_merge([
                'this' => $view
            ], $params));
        };

        $obInitialLevel = ob_get_level();
        ob_start();
        PHP_VERSION_ID >= 80000 ? ob_implicit_flush(false) : ob_implicit_flush(0);
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
