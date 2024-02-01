<?php

declare(strict_types=1);

namespace Yiisoft\View\Twig;

use Throwable;
use Twig\Environment;
use Yiisoft\View\Template;
use Yiisoft\View\TemplateRendererInterface;

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
    public function __construct(private Environment $environment)
    {
    }

    public function render(Template $template): string
    {
        $view = $template->getView();
        $templateFile = str_replace(
            [$view->getBasePath(), $template->getViewContext()?->getViewPath() ?? ''],
            '',
            $template->getTemplate()
        );

        $obInitialLevel = ob_get_level();
        ob_start();
        ob_implicit_flush(false);

        try {
            $this->environment->display($templateFile, array_merge($template->getParameters(), ['this' => $view]));
            return ob_get_clean();
        } catch (Throwable $e) {
            while (ob_get_level() > $obInitialLevel) {
                ob_end_clean();
            }
            throw $e;
        }
    }
}
