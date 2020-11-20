<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Twig\Tests;

use Yiisoft\Files\FileHelper;

final class WebViewTest extends TestCase
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

        FileHelper::createDirectory($this->testViewPath);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        FileHelper::removeDirectory($this->testViewPath);
    }

    public function testLayout(): void
    {
        $content = $this->webView->render('//index.twig', ['name' => 'Javharbek Abdulatipov']);

        $result = $this->webView->renderFile(
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
