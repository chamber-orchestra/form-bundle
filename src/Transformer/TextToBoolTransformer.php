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

readonly class TextToBoolTransformer implements DataTransformerInterface
{
    public function __construct(
        private array $trueValues,
        private array $falseValues,
    ) {
    }

    public function transform($value): bool
    {
        if (null === $value) {
            return false;
        }

        if (!\is_bool($value)) {
            throw new TransformationFailedException('Expected a boolean');
        }

        return $value;
    }

    public function reverseTransform($value): bool
    {
        if (null === $value) {
            return false;
        }
        if (\in_array($value, $this->trueValues, true)) {
            return true;
        }
        if (\in_array($value, $this->falseValues, true)) {
            return false;
        }

        throw new TransformationFailedException('Invalid value.');
    }
}
