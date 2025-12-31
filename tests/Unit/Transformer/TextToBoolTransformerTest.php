<?php

declare(strict_types=1);

namespace Tests\Unit\Transformer;

use ChamberOrchestra\FormBundle\Exception\TransformationFailedException;
use ChamberOrchestra\FormBundle\Transformer\TextToBoolTransformer;
use PHPUnit\Framework\TestCase;

final class TextToBoolTransformerTest extends TestCase
{
    public function testTransformAcceptsBoolean(): void
    {
        $transformer = new TextToBoolTransformer([1], [0]);

        self::assertTrue($transformer->transform(true));
        self::assertFalse($transformer->transform(null));
    }

    public function testTransformRejectsNonBoolean(): void
    {
        $transformer = new TextToBoolTransformer([1], [0]);

        $this->expectException(TransformationFailedException::class);

        $transformer->transform('yes');
    }

    public function testReverseTransformUsesConfiguredValues(): void
    {
        $transformer = new TextToBoolTransformer(['yes'], ['no']);

        self::assertTrue($transformer->reverseTransform('yes'));
        self::assertFalse($transformer->reverseTransform('no'));
    }

    public function testReverseTransformRejectsUnknownValue(): void
    {
        $transformer = new TextToBoolTransformer(['yes'], ['no']);

        $this->expectException(TransformationFailedException::class);

        $transformer->reverseTransform('maybe');
    }
}
