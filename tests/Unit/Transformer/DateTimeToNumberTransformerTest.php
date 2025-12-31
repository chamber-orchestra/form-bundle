<?php

declare(strict_types=1);

namespace Tests\Unit\Transformer;

use ChamberOrchestra\FormBundle\Transformer\DateTimeToNumberTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\DatePoint;

final class DateTimeToNumberTransformerTest extends TestCase
{
    public function testTransformAndReverseTransform(): void
    {
        $transformer = new DateTimeToNumberTransformer(\DateTimeImmutable::class);
        $date = new \DateTimeImmutable('2024-01-01 00:00:00');

        $timestamp = $transformer->transform($date);
        $restored = $transformer->reverseTransform($timestamp);

        self::assertSame($date->getTimestamp(), $timestamp);
        self::assertInstanceOf(\DateTimeImmutable::class, $restored);
        self::assertSame($date->getTimestamp(), $restored->getTimestamp());
    }

    public function testDatePointTransformAndReverseTransform(): void
    {
        $transformer = new DateTimeToNumberTransformer(DatePoint::class);
        $date = new DatePoint('2024-02-01 12:00:00');

        $timestamp = $transformer->transform($date);
        $restored = $transformer->reverseTransform($timestamp);

        self::assertSame($date->getTimestamp(), $timestamp);
        self::assertInstanceOf(DatePoint::class, $restored);
        self::assertSame($date->getTimestamp(), $restored->getTimestamp());
    }

    public function testConstructorRejectsInvalidClass(): void
    {
        $this->expectException(\TypeError::class);

        new DateTimeToNumberTransformer(\stdClass::class);
    }
}
