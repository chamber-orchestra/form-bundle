<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use ChamberOrchestra\FormBundle\Exception\TranslatableExceptionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;

final class TranslatableExceptionInterfaceTest extends TestCase
{
    public function testTranslatableMessageIsReturned(): void
    {
        $exception = new class() extends \RuntimeException implements TranslatableExceptionInterface {
            public function getTranslatableMessage(): TranslatableInterface
            {
                return new TranslatableMessage('error.key');
            }
        };

        $message = $exception->getTranslatableMessage();

        self::assertInstanceOf(TranslatableInterface::class, $message);
        self::assertSame('error.key', $message->getMessage());
    }
}
