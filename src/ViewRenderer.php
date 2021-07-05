<?php

declare(strict_types=1);

namespace Yiisoft\View\Twig;

use Throwable;
use Twig\Environment;
use Yiisoft\View\BaseView;
use Yiisoft\View\TemplateRendererInterface;

use function array_merge;
use function ob_end_clean;
use function ob_get_clean;
use function ob_get_level;
use function ob_implicit_flush;
use function ob_start;
use function str_replace;

/**
 * ViewRenderer allows using Twig with a View service.
 */
final class ViewRenderer implements TemplateRendererInterface
{
    private Environment $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function render(BaseView $view, string $template, array $params): string
    {
        $environment = $this->environment;
        $renderer = function () use ($view, $template, $params, $environment): void {
            $template = str_replace('\\', '/', $template);
            $basePath = str_replace('\\', '/', $view->getBasePath());
            $file = str_replace($basePath, '', $template);

            echo $environment->render($file, array_merge($params, ['this' => $view]));
        };

        $obInitialLevel = ob_get_level();
        ob_start();
        /** @psalm-suppress PossiblyFalseArgument */
        PHP_VERSION_ID >= 80000 ? ob_implicit_flush(false) : ob_implicit_flush(0);

        try {
            /** @psalm-suppress PossiblyInvalidFunctionCall */
            $renderer->bindTo($view)($template, $params);
            return ob_get_clean();
        } catch (Throwable $e) {
            while (ob_get_level() > $obInitialLevel) {
                ob_end_clean();
            }
            throw $e;
        }
    }
}
