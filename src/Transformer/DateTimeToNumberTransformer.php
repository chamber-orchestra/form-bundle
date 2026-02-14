<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

readonly class DateTimeToNumberTransformer implements DataTransformerInterface
{
    public function __construct(private string $class)
    {
        if (!\in_array(\DateTimeInterface::class, \class_implements($class) ?: [])) {
            throw new \InvalidArgumentException(
                \sprintf('Class "%s" must implement %s.', $class, \DateTimeInterface::class)
            );
        }
    }

    public function transform($value): int|null
    {
        if (null !== $value && !($value instanceof $this->class)) {
            throw new \TypeError(\sprintf('Passed value must be of type %s or null.', $this->class));
        }

        return $value?->getTimestamp();
    }

    public function reverseTransform($value): \DateTimeInterface|null
    {
        if (null !== $value && !\is_int($value)) {
            throw new \TypeError(
                \sprintf('Passed value must be of type %s or null, %s given.', 'int', \get_debug_type($value))
            );
        }

        return $value !== null ? (new $this->class)->setTimestamp($value) : null;
    }
}