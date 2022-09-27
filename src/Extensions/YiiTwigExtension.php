<?php

declare(strict_types=1);

namespace Yiisoft\View\Twig\Extensions;

use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * YiiTwigExtension adds additional functionality to the Twig engine.
 */
final class YiiTwigExtension extends AbstractExtension
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
                /** @return mixed */
                fn (string $id) => $this->container->get($id),
            ),
        ];
    }
}
