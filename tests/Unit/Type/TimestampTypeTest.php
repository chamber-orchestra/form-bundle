<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Type;

use ChamberOrchestra\FormBundle\Transformer\DateTimeToNumberTransformer;
use ChamberOrchestra\FormBundle\Type\TimestampType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TimestampTypeTest extends TestCase
{
    public function testConfigureOptionsSetsDefaults(): void
    {
        $type = new TimestampType();
        $resolver = new OptionsResolver();

        $type->configureOptions($resolver);
        $options = $resolver->resolve();

        self::assertFalse($options['grouping']);
        self::assertSame(0, $options['scale']);
    }

    public function testBuildFormAddsDatePointTransformer(): void
    {
        $type = new TimestampType();
        $builder = $this->createMock(FormBuilderInterface::class);

        $builder
            ->expects($this->once())
            ->method('addModelTransformer')
            ->with($this->callback(static function ($transformer): bool {
                if (!$transformer instanceof DateTimeToNumberTransformer) {
                    return false;
                }

                $result = $transformer->reverseTransform(0);

                return $result instanceof DatePoint;
            }));

        $type->buildForm($builder, []);
    }

    public function testParentIsNumberType(): void
    {
        $type = new TimestampType();

        self::assertSame(NumberType::class, $type->getParent());
    }
}
