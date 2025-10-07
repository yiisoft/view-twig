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
use Yiisoft\View\Template;
use Yiisoft\View\Twig\TemplateRenderer;
use Yiisoft\View\Twig\Tests\Support\BeginBody;
use Yiisoft\View\Twig\Tests\Support\EndBody;
use Yiisoft\View\Twig\Tests\Support\ErrorContent;
use Yiisoft\View\Twig\Tests\Support\SimpleExtension;
use Yiisoft\View\WebView;

final class TemplateRendererTest extends TestCase
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

        $this->assertStringContainsString('Begin Body', $result);
        $this->assertStringContainsString('Javharbek Abdulatipov', $result);
        $this->assertStringNotContainsString('{{ name }}', $result);
        $this->assertStringContainsString('End Body', $result);
    }

    public function testExceptionDuringRendering(): void
    {
        $container = $this->getContainer();
        $view = $this->getView($container);
        $renderer = $container->get(TemplateRenderer::class);

        $obInitialLevel = ob_get_level();

        try {
            $renderer->render($view, 'error.twig', []);
        } catch (RuntimeError) {
        }

        $this->assertSame(ob_get_level(), $obInitialLevel);
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

        $container = new SimpleContainer([
            Aliases::class => $aliases,
            BeginBody::class => new BeginBody(),
            EndBody::class => new EndBody(),
            ErrorContent::class => new ErrorContent(),
            Environment::class => $twig,
            TemplateRenderer::class => new TemplateRenderer($twig),
        ]);

        $twig->addExtension(new SimpleExtension($container));

        return $container;
    }

    private function getView(SimpleContainer|null $container = null): WebView
    {
        $container ??= $this->getContainer();

        return (new WebView($container->get(Aliases::class)->get('@views')))
            ->withRenderers(['twig' => new TemplateRenderer($container->get(Environment::class))])
            ->withFallbackExtension('twig');
    }
}
