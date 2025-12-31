<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use ChamberOrchestra\FormBundle\Exception\ExceptionInterface;
use ChamberOrchestra\FormBundle\Exception\SubmittedFormRequiredException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class SubmittedFormRequiredExceptionTest extends TestCase
{
    public function testMessageIncludesFormType(): void
    {
        $exception = new SubmittedFormRequiredException('ExampleType');

        self::assertInstanceOf(BadRequestHttpException::class, $exception);
        self::assertInstanceOf(ExceptionInterface::class, $exception);
        self::assertSame('Submitted form of type ExampleType required', $exception->getMessage());
    }
}
