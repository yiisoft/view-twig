<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Twig\Extensions;

use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * YiiTwigExtension adds additional functionality to the Twig engine.
 */
final class YiiTwigExtension extends AbstractExtension
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
