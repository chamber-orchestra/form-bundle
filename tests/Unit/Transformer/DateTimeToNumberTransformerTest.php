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

    public function testReverseTransformHandlesTimestampZero(): void
    {
        $transformer = new DateTimeToNumberTransformer(\DateTimeImmutable::class);

        $result = $transformer->reverseTransform(0);

        self::assertInstanceOf(\DateTimeImmutable::class, $result);
        self::assertSame(0, $result->getTimestamp());
    }

    public function testTransformReturnsNullForNull(): void
    {
        $transformer = new DateTimeToNumberTransformer(\DateTimeImmutable::class);

        self::assertNull($transformer->transform(null));
    }

    public function testReverseTransformReturnsNullForNull(): void
    {
        $transformer = new DateTimeToNumberTransformer(\DateTimeImmutable::class);

        self::assertNull($transformer->reverseTransform(null));
    }

    public function testTransformRejectsInvalidType(): void
    {
        $transformer = new DateTimeToNumberTransformer(\DateTimeImmutable::class);

        $this->expectException(\TypeError::class);

        $transformer->transform('not-a-date');
    }

    public function testReverseTransformRejectsInvalidType(): void
    {
        $transformer = new DateTimeToNumberTransformer(\DateTimeImmutable::class);

        $this->expectException(\TypeError::class);

        $transformer->reverseTransform('not-an-int');
    }

    public function testConstructorRejectsInvalidClass(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new DateTimeToNumberTransformer(\stdClass::class);
    }
}
