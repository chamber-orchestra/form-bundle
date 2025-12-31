<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use ChamberOrchestra\FormBundle\Exception\ExceptionInterface;
use ChamberOrchestra\FormBundle\Exception\LogicException;
use PHPUnit\Framework\TestCase;

final class LogicExceptionTest extends TestCase
{
    public function testImplementsExceptionInterface(): void
    {
        $exception = new LogicException('message');

        self::assertInstanceOf(ExceptionInterface::class, $exception);
    }
}
