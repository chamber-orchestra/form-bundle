<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Transformer;

use ChamberOrchestra\FormBundle\Exception\TransformationFailedException;
use ChamberOrchestra\FormBundle\Transformer\ArrayToStringTransformer;
use PHPUnit\Framework\TestCase;

final class ArrayToStringTransformerTest extends TestCase
{
    public function testTransformAndReverseTransform(): void
    {
        $transformer = new ArrayToStringTransformer();

        self::assertSame('1, 2, 3', $transformer->transform([1, 2, 3]));
        self::assertSame([], $transformer->reverseTransform(''));
        self::assertSame(['123', '456'], $transformer->reverseTransform('123, 456'));
    }

    public function testReverseTransformTrimsWhitespace(): void
    {
        $transformer = new ArrayToStringTransformer();

        self::assertSame(['foo', 'bar', 'baz'], $transformer->reverseTransform('foo, bar, baz'));
    }

    public function testTransformRejectsInvalidType(): void
    {
        $transformer = new ArrayToStringTransformer();

        $this->expectException(TransformationFailedException::class);

        $transformer->transform('not-array');
    }
}
