<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Twig\Extensions;

use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

/**
 * Class YiiTwigExtension
 * @package Yiisoft\Yii\Twig\Extensions
 */
class YiiTwigExtension extends AbstractExtension implements GlobalsInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        $options = [
            'is_safe',
        ];

        return [
            new TwigFunction('get', function ($id) {
                return $this->container->get($id);
            }, $options)
        ];
    }

    public function getGlobals(): array
    {
        return [
            'container' => $this->container,
        ];
    }
}
