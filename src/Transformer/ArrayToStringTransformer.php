<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\Transformer;

use ChamberOrchestra\FormBundle\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;

readonly class ArrayToStringTransformer implements DataTransformerInterface
{
    public function transform($value): string
    {
        if (null !== $value && !\is_array($value)) {
            throw TransformationFailedException::notAllowedType($value, ['array', 'null']);
        }

        return null !== $value ? \implode(', ', $value) : '';
    }

    public function reverseTransform($value): array
    {
        if (null !== $value && !\is_string($value)) {
            throw TransformationFailedException::notAllowedType($value, ['string', 'null']);
        }

        if (null === $value || '' === $value) {
            return [];
        }

        return \array_map(
            fn(string $value): string => \preg_replace('/[^\d]/', '', $value),
            \explode(',', $value)
        );
    }
}
