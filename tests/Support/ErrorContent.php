<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Twig\Tests\Support;

use Twig\Error\RuntimeError;

use function ob_start;

final class ErrorContent
{
    public function content(): string
    {
        ob_start();
        throw new RuntimeError('test');
    }
}
