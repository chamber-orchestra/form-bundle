<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Exception;

use ChamberOrchestra\FormBundle\Exception\ExceptionInterface;
use ChamberOrchestra\FormBundle\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class InvalidArgumentExceptionTest extends TestCase
{
    public function testImplementsExceptionInterface(): void
    {
        $exception = new InvalidArgumentException('message');

        self::assertInstanceOf(ExceptionInterface::class, $exception);
    }
}
