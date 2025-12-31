<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use ChamberOrchestra\FormBundle\Exception\ExceptionInterface;
use ChamberOrchestra\FormBundle\Exception\XmlHttpRequestRequiredException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

final class XmlHttpRequestRequiredExceptionTest extends TestCase
{
    public function testDefaultMessage(): void
    {
        $exception = new XmlHttpRequestRequiredException();

        self::assertInstanceOf(NotAcceptableHttpException::class, $exception);
        self::assertInstanceOf(ExceptionInterface::class, $exception);
        self::assertSame('XML HTTP request required.', $exception->getMessage());
    }

    public function testCustomMessage(): void
    {
        $exception = new XmlHttpRequestRequiredException('Custom message');

        self::assertSame('Custom message', $exception->getMessage());
    }
}
