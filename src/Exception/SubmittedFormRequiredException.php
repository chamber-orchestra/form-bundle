<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SubmittedFormRequiredException extends BadRequestHttpException implements ExceptionInterface
{
    public function __construct(string $type, ?\Exception $previous = null)
    {
        parent::__construct(\sprintf('Submitted form of type %s required', $type), $previous);
    }
}
