<?php

declare(strict_types=1);

namespace Tests\Integrational;

use ChamberOrchestra\FormBundle\Exception\TranslatableExceptionInterface;
use ChamberOrchestra\FormBundle\Serializer\Normalizer\ProblemNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;

final class ProblemNormalizerIntegrationTest extends TestCase
{
    public function testNormalizerAddsTranslatedMessage(): void
    {
        $kernel = new TestKernel('test', true);
        $kernel->boot();

        $normalizer = $kernel->getContainer()->get(ProblemNormalizer::class);
        $exception = new class() extends \RuntimeException implements TranslatableExceptionInterface {
            public function getTranslatableMessage(): TranslatableInterface
            {
                return new TranslatableMessage('error.key');
            }
        };

        $flatten = FlattenException::createFromThrowable(new \RuntimeException('Boom'));
        $data = $normalizer->normalize($flatten, null, ['exception' => $exception]);

        $kernel->shutdown();

        self::assertSame('error.key', $data['message']);
    }
}
