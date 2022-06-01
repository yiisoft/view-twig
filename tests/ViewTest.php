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
use Yiisoft\Test\Support\EventDispatcher\SimpleEventDispatcher;
use Yiisoft\View\TemplateRendererInterface;
use Yiisoft\View\WebView;
use Yiisoft\View\Twig\Extensions\YiiTwigExtension;
use Yiisoft\View\Twig\Tests\Support\BeginBody;
use Yiisoft\View\Twig\Tests\Support\EndBody;
use Yiisoft\View\Twig\Tests\Support\ErrorContent;
use Yiisoft\View\Twig\ViewRenderer;

final class ViewTest extends TestCase
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
            ->render('/index.twig', ['name' => 'Javharbek Abdulatipov']);

        $result = $this
            ->getView()
            ->renderFile($this->layoutPath, ['content' => $content]);

        $this->assertStringContainsString('Begin Body', $result);
        $this->assertStringContainsString('Javharbek Abdulatipov', $result);
        $this->assertStringNotContainsString('{{ name }}', $result);
        $this->assertStringContainsString('End Body', $result);
    }

    public function testExtension(): void
    {
        $container = $this->getContainer();
        $extension = new YiiTwigExtension($container);
        $functionGet = $extension->getFunctions()[0];

        $this->assertSame($container->get(Aliases::class), ($functionGet->getCallable())(Aliases::class));
        $this->assertSame($container->get(BeginBody::class), ($functionGet->getCallable())(BeginBody::class));
        $this->assertSame($container->get(EndBody::class), ($functionGet->getCallable())(EndBody::class));
        $this->assertSame($container->get(ErrorContent::class), ($functionGet->getCallable())(ErrorContent::class));
        $this->assertSame($container->get(Environment::class), ($functionGet->getCallable())(Environment::class));
    }

    public function testExceptionDuringRendering(): void
    {
        $container = $this->getContainer();
        $view = $this->getView($container);
        $renderer = $container->get(TemplateRendererInterface::class);

        $obInitialLevel = ob_get_level();

        try {
            $renderer->render($view, __DIR__ . '/public/views/error.twig', []);
        } catch (RuntimeError $e) {
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
            TemplateRendererInterface::class => new ViewRenderer($twig),
        ]);

        $twig->addExtension(new YiiTwigExtension($container));

        return $container;
    }

    private function getView(SimpleContainer $container = null): WebView
    {
        $container ??= $this->getContainer();

        return (new WebView($container
            ->get(Aliases::class)
            ->get('@views'), new SimpleEventDispatcher()))
            ->withRenderers(['twig' => new ViewRenderer($container->get(Environment::class))])
            ->withDefaultExtension('twig')
            ;
    }
}
