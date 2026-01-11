<?php

declare(strict_types=1);

namespace Yiisoft\View\Twig\Tests;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Loader\FilesystemLoader;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Files\FileHelper;
use Yiisoft\Test\Support\Container\SimpleContainer;
use Yiisoft\View\Twig\TwigTemplateRenderer;
use Yiisoft\View\WebView;

final class TwigTemplateRendererTest extends TestCase
{
    private string $layoutPath;
    private string $tempDirectory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDirectory = __DIR__ . '/public/tmp/View';
        FileHelper::ensureDirectory($this->tempDirectory);
        $this->layoutPath = dirname(__DIR__) . '/tests/public/views/layout.twig';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        FileHelper::removeDirectory($this->tempDirectory);
    }

    public function testLayout(): void
    {
        $content = $this
            ->getView()
            ->render('index.twig', ['name' => 'Javharbek Abdulatipov']);

        $result = $this
            ->getView()
            ->render($this->layoutPath, ['content' => $content]);

        $this->assertStringContainsString('Yii Demo (Twig)', $result);
        $this->assertStringContainsString('Javharbek Abdulatipov', $result);
        $this->assertStringNotContainsString('{{ name }}', $result);
    }

    public function testExceptionDuringRendering(): void
    {
        $container = $this->getContainer();
        $view = $this->getView($container);
        $renderer = $container->get(TwigTemplateRenderer::class);

        $obInitialLevel = ob_get_level();

        try {
            $renderer->render($view, 'error.twig', []);
        } catch (RuntimeError) {
            $this->assertSame(ob_get_level(), $obInitialLevel);
        }

        $this->assertSame(ob_get_level(), $obInitialLevel);
    }

    public function testRenderWithExceptionInTemplate(): void
    {
        $container = $this->getContainer();
        $view = $this->getView($container);
        $renderer = $container->get(TwigTemplateRenderer::class);

        $this->expectException(RuntimeError::class);

        $renderer->render($view, 'exception.twig', []);
    }

    private function getContainer(): SimpleContainer
    {
        $aliases = new Aliases([
            '@root' => dirname(__DIR__),
            '@public' => '@root/tests/public',
            '@basePath' => '@public/assets',
            '@views' => '@public/views',
            '@baseUrl' => '/baseUrl',
        ]);

        $twig = new Environment(new FilesystemLoader($aliases->get('@views')), ['charset' => 'utf-8']);

        return new SimpleContainer([
            Aliases::class => $aliases,
            Environment::class => $twig,
            TwigTemplateRenderer::class => new TwigTemplateRenderer($twig),
        ]);
    }

    private function getView(SimpleContainer|null $container = null): WebView
    {
        $container ??= $this->getContainer();
        $basePath = $container->get(Aliases::class)->get('@views');

        return (new WebView($basePath))
            ->withContextPath($basePath)
            ->withRenderers(['twig' => new TwigTemplateRenderer($container->get(Environment::class))])
            ->withFallbackExtension('twig');
    }
}
