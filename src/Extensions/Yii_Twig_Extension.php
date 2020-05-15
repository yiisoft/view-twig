<?php

namespace Yiisoft\Yii\Twig\Extensions;

use Psr\Container\ContainerInterface;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

/**
 * Class Yii_Twig_Extension
 * @package Yiisoft\Yii\Twig\Extensions
 */
class Yii_Twig_Extension extends \Twig\Extension\AbstractExtension implements GlobalsInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
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
