<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Twig\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Container\ContainerInterface;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Di\Container;
use Yiisoft\View\WebView;

abstract class TestCase extends BaseTestCase
{
    private ?ContainerInterface $container = null;
    protected WebView $webView;

    protected function setUp(): void
    {
        parent::setUp();

        $config = require Builder::path('tests');
        $this->container = new Container($config);
        $this->webView = $this->container->get(WebView::class);
    }

    protected function tearDown(): void
    {
        $this->container = null;
        parent::tearDown();
    }
}
