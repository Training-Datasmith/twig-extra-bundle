<?php

declare(strict_types=1);

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig\Extra\TwigExtraBundle;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * @internal
 */
final class LeagueCommonMarkConverterFactory
{
    /**
     * @param ExtensionInterface[] $extensions
     */
    public function __construct(private readonly iterable $extensions, private readonly array $config = [])
    {
    }

    public function __invoke(): CommonMarkConverter
    {
        $converter = new CommonMarkConverter($this->config);

        foreach ($this->extensions as $extension) {
            $converter->getEnvironment()->addExtension($extension);
        }

        return $converter;
    }
}
