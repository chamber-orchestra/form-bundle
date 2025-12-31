<?php

declare(strict_types=1);

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
