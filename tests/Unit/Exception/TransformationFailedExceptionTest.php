<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use ChamberOrchestra\FormBundle\Exception\TransformationFailedException;
use PHPUnit\Framework\TestCase;

final class TransformationFailedExceptionTest extends TestCase
{
    public function testNotAllowedTypeMessageContainsType(): void
    {
        $exception = TransformationFailedException::notAllowedType(new \stdClass(), ['string']);

        self::assertStringContainsString('allowed types', $exception->getMessage());
        self::assertStringContainsString('stdClass', $exception->getMessage());
    }
}
