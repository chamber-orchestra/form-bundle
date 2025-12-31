<?php

declare(strict_types=1);

namespace Tests\Unit\Serializer\Normalizer;

use ChamberOrchestra\FormBundle\Exception\TranslatableExceptionInterface;
use ChamberOrchestra\FormBundle\Serializer\Normalizer\ProblemNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProblemNormalizerTest extends TestCase
{
    public function testAddsTranslatedMessageFromException(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->once())
            ->method('trans')
            ->with('error.key')
            ->willReturn('Translated message');

        $exception = new class() extends \RuntimeException implements TranslatableExceptionInterface {
            public function getTranslatableMessage(): TranslatableInterface
            {
                return new TranslatableMessage('error.key');
            }
        };

        $normalizer = new ProblemNormalizer($translator);
        $flatten = FlattenException::createFromThrowable(new \RuntimeException('Boom'));
        $data = $normalizer->normalize($flatten, null, ['exception' => $exception]);

        self::assertSame('Translated message', $data['message']);
    }
}
