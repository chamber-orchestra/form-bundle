<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\Exception;

class TransformationFailedException extends \Symfony\Component\Form\Exception\TransformationFailedException implements ExceptionInterface
{
    public static function notAllowedType($id, array $allowedTypes): TransformationFailedException
    {
        return new self(
            \sprintf(
                "Passed value is not one of allowed types, allowed types '%s', passed '%s'.",
                \implode(',', $allowedTypes),
                \is_object($id) ? \get_class($id) : \gettype($id)
            )
        );
    }
}
