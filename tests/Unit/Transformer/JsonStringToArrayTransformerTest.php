<?php

declare(strict_types=1);

namespace Tests\Unit\Transformer;

use ChamberOrchestra\FormBundle\Exception\TransformationFailedException;
use ChamberOrchestra\FormBundle\Transformer\JsonStringToArrayTransformer;
use PHPUnit\Framework\TestCase;

final class JsonStringToArrayTransformerTest extends TestCase
{
    public function testTransformAndReverseTransform(): void
    {
        $transformer = new JsonStringToArrayTransformer();

        $json = $transformer->transform(['id' => 1]);
        $array = $transformer->reverseTransform('{"id":1}');

        self::assertSame('{"id":1}', $json);
        self::assertSame(['id' => 1], $array);
    }

    public function testReverseTransformRejectsInvalidJson(): void
    {
        $transformer = new JsonStringToArrayTransformer();

        $this->expectException(TransformationFailedException::class);

        $transformer->reverseTransform('{');
    }
}
