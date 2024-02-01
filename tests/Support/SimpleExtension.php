<?php

declare(strict_types=1);

namespace Yiisoft\View\Twig\Tests\Support;

use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * YiiTwigExtension adds additional functionality to the Twig engine.
 */
final class SimpleExtension extends AbstractExtension
{
    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'get',
                fn (string $id): mixed => $this->container->get($id),
            ),
        ];
    }
}
