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
            new TwigFunction(
                'array_helper',
                fn (string $method, mixed ...$args) => forward_static_call_array(
                    ['\\Yiisoft\\Arrays\\ArrayHelper', $method],
                    $args
                ),
                ['is_variadic' => true]
            ),
            new TwigFunction(
                'array_sort',
                fn (string $method, mixed ...$args) => forward_static_call_array(
                    ['\\Yiisoft\\Arrays\\ArraySorter', $method],
                    $args
                ),
                ['is_variadic' => true]
            ),
            new TwigFunction(
                'file_helper',
                fn (string $method, mixed ...$args) => forward_static_call_array(
                    ['\\Yiisoft\\Files\\FileHelper', $method],
                    $args
                ),
                ['is_variadic' => true]
            ),
            new TwigFunction(
                'form_field',
                fn (string $method, mixed ...$args) => forward_static_call_array(
                    ['\\Yiisoft\\Form\\Field', $method],
                    $args
                ),
                ['is_variadic' => true]
            ),
            new TwigFunction(
                'html',
                fn (string $method, mixed ...$args) => forward_static_call_array(
                    ['\\Yiisoft\\Html\\Html', $method],
                    $args
                ),
                ['is_variadic' => true]
            ),
            new TwigFunction(
                'json',
                fn (string $method, mixed ...$args) => forward_static_call_array(
                    ['\\Yiisoft\\Json\\Json', $method],
                    $args
                ),
                ['is_variadic' => true]
            ),
            new TwigFunction(
                'numeric_helper',
                fn (string $method, mixed ...$args) => forward_static_call_array(
                    ['\\Yiisoft\\Strings\\NumericHelper', $method],
                    $args
                ),
                ['is_variadic' => true]
            ),
            new TwigFunction(
                'string_helper',
                fn (string $method, mixed ...$args) => forward_static_call_array(
                    ['\\Yiisoft\\Strings\\StringHelper', $method],
                    $args
                ),
                ['is_variadic' => true]
            ),
        ];
    }
}
