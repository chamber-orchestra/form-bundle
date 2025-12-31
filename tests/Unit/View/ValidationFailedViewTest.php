<?php

declare(strict_types=1);

namespace Tests\Unit\View;

use ChamberOrchestra\FormBundle\View\ValidationFailedView;
use ChamberOrchestra\FormBundle\View\ViolationView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ValidationFailedViewTest extends TestCase
{
    public function testBuildsDetailFromViolations(): void
    {
        $violations = [
            new ViolationView('id', 'First', [], 'field'),
            new ViolationView('id', 'Second', [], 'field'),
        ];

        $view = new ValidationFailedView($violations);
        $normalizer = $this->createMock(NormalizerInterface::class);

        $normalizer
            ->expects($this->once())
            ->method('normalize')
            ->willReturnCallback(static fn(array $data) => $data);

        $data = $view->normalize($normalizer);

        self::assertSame("First\nSecond", $data['detail']);
        self::assertSame($violations, $data['violations']);
        self::assertSame(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $view->getStatus());
    }

    public function testNormalizeUsesNormalizer(): void
    {
        $violations = [new ViolationView('id', 'First', [], 'field')];
        $view = new ValidationFailedView($violations, 'Validation Failed');
        $normalizer = $this->createMock(NormalizerInterface::class);

        $normalizer
            ->expects($this->once())
            ->method('normalize')
            ->willReturnCallback(static fn(array $data) => $data);

        $data = $view->normalize($normalizer);

        self::assertSame('Validation Failed', $data['title']);
        self::assertSame('https://symfony.com/errors/validation', $data['type']);
        self::assertSame('First', $data['detail']);
    }
}
