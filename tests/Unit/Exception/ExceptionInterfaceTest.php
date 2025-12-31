<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use ChamberOrchestra\FormBundle\Exception\ExceptionInterface;
use PHPUnit\Framework\TestCase;

final class ExceptionInterfaceTest extends TestCase
{
    public function testInterfaceExists(): void
    {
        self::assertTrue(\interface_exists(ExceptionInterface::class));
    }
}
