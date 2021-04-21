<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Twig\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Files\FileHelper;
use Yiisoft\Test\Support\Container\SimpleContainer;
use Yiisoft\Test\Support\EventDispatcher\SimpleEventDispatcher;
use Yiisoft\View\View;
use Yiisoft\Yii\Twig\Extensions\YiiTwigExtension;
use Yiisoft\Yii\Twig\ViewRenderer;

final class ViewTest extends TestCase
{
    private string $layoutPath;

    /**
     * @var string path for the test files.
     */
    private string $testViewPath = '';

    protected function setUp(): void
    {
        parent::setUp();

        $dataDir = dirname(__DIR__) . '/tests/public/views';
        $this->layoutPath = $dataDir . '/layout.twig';
        $this->testViewPath = sys_get_temp_dir() . '/' . str_replace('\\', '_', self::class) . uniqid('', false);

        FileHelper::ensureDirectory($this->testViewPath);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        FileHelper::removeDirectory($this->testViewPath);
    }

    protected function getView(): View
    {
        $aliases = new Aliases([
            '@root' => dirname(__DIR__, 1),
            '@public' => '@root/tests/public',
            '@basePath' => '@public/assets',
            '@views' => '@public/views',
            '@baseUrl' => '/baseUrl',
        ]);

        $loader = new \Twig\Loader\FilesystemLoader($aliases->get('@views'));
        $twig = new \Twig\Environment(
            $loader,
            [
                'charset' => 'utf-8',
            ]
        );

        $webView = (new View(
            $aliases->get('@views'),
            new SimpleEventDispatcher(),
            new NullLogger()
        ))
        ->withDefaultExtension('twig')
        ->withRenderers([
            'twig' => new ViewRenderer($twig),
        ]);

        $twig->addExtension(new YiiTwigExtension(new SimpleContainer([
            Html::class => new Html(),
        ])));

        return $webView;
    }

    public function testLayout(): void
    {
        $content = $this->getView()->render('//index.twig', ['name' => 'Javharbek Abdulatipov']);

        $result = $this->getView()->renderFile(
            $this->layoutPath,
            [
                'content' => $content,
            ]
        );

        $this->assertStringContainsString('Javharbek Abdulatipov', $result);
        $this->assertStringContainsString('Hello World', $result);
        $this->assertStringNotContainsString('helloWorld', $result);
        $this->assertStringNotContainsString('{{ name }}', $result);
    }
}
