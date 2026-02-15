<?php

declare(strict_types=1);

namespace Tests\Unit\View;

use ChamberOrchestra\FormBundle\View\FailureView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class FailureViewTest extends TestCase
{
    public function testStatusAndHeaders(): void
    {
        $view = new FailureView(JsonResponse::HTTP_BAD_REQUEST, 'Bad');

        self::assertSame(JsonResponse::HTTP_BAD_REQUEST, $view->getStatus());
        self::assertSame(['Content-Type' => 'application/problem+json'], $view->getHeaders());
    }

    public function testNormalizeUsesNormalizer(): void
    {
        $view = new FailureView(JsonResponse::HTTP_BAD_REQUEST, 'Bad');
        $normalizer = $this->createMock(NormalizerInterface::class);

        $normalizer
            ->expects($this->once())
            ->method('normalize')
            ->willReturnCallback(static fn(array $data) => $data);

        $data = $view->normalize($normalizer);

        self::assertSame('Bad', $data['title']);
        self::assertSame('https://datatracker.ietf.org/doc/html/rfc9110#section-15', $data['type']);
    }
}
