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

/** @implements DataTransformerInterface<\DateTimeInterface|null, int|null> */
readonly class DateTimeToNumberTransformer implements DataTransformerInterface
{
    /** @param class-string<\DateTimeInterface> $class */
    public function __construct(private string $class)
    {
        if (!\in_array(\DateTimeInterface::class, \class_implements($class) ?: [], true)) {
            throw new \InvalidArgumentException(\sprintf('Class "%s" must implement %s.', $class, \DateTimeInterface::class));
        }
    }

    public function transform(mixed $value): ?int
    {
        if (null !== $value && !($value instanceof $this->class)) {
            throw new \TypeError(\sprintf('Passed value must be of type %s or null.', $this->class));
        }

        return $value instanceof \DateTimeInterface ? $value->getTimestamp() : null;
    }

    public function reverseTransform(mixed $value): ?\DateTimeInterface
    {
        if (null !== $value && !\is_int($value)) {
            throw new \TypeError(\sprintf('Passed value must be of type %s or null, %s given.', 'int', \get_debug_type($value)));
        }

        if (null === $value) {
            return null;
        }

        /** @var \DateTime|\DateTimeImmutable $dateTime */
        $dateTime = new $this->class();

        return $dateTime->setTimestamp($value);
    }
}
