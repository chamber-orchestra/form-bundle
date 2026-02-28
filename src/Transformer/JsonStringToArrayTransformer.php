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

/** @implements DataTransformerInterface<array<mixed>|null, string|null> */
readonly class JsonStringToArrayTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        try {
            $value = \json_encode($value, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new TransformationFailedException('Could not encode array into json.', $e->getCode(), $e);
        }

        return $value;
    }

    /** @return array<mixed>|null */
    public function reverseTransform(mixed $value): ?array
    {
        if (null === $value || '' === $value) {
            return null;
        }

        try {
            /** @var array<mixed> $decoded */
            $decoded = \json_decode($value, true, 512, \JSON_BIGINT_AS_STRING | \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new TransformationFailedException('Could not parse JSON into array.', $e->getCode(), $e);
        }

        return $decoded;
    }
}
