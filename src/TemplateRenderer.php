<?php

declare(strict_types=1);

namespace Yiisoft\View\Twig;

use Throwable;
use Twig\Environment;
use Yiisoft\View\TemplateRendererInterface;

use Yiisoft\View\ViewInterface;

use function array_merge;
use function ob_end_clean;
use function ob_get_clean;
use function ob_get_level;
use function ob_implicit_flush;
use function ob_start;
use function str_replace;

/**
 * TemplateRenderer allows using Twig with a View service.
 */
final class TemplateRenderer implements TemplateRendererInterface
{
    public function __construct(private readonly Environment $environment)
    {
    }

    public function render(ViewInterface $view, string $template, array $parameters): string
    {
        $templateFile = str_replace(
            $view->getBasePath(),
            '',
            $template
        );

        $obInitialLevel = ob_get_level();
        ob_start();
        ob_implicit_flush(false);

        try {
            $this->environment->display($templateFile, array_merge($parameters, ['this' => $view]));
            return ob_get_clean();
        } catch (Throwable $e) {
            while (ob_get_level() > $obInitialLevel) {
                ob_end_clean();
            }
            throw $e;
        }
    }
}
