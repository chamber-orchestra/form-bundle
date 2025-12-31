<?php

declare(strict_types=1);

namespace Tests\Unit\Type;

use ChamberOrchestra\FormBundle\Transformer\DateTimeToNumberTransformer;
use ChamberOrchestra\FormBundle\Type\TimestampType;
use PHPUnit\Framework\TestCase;
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

        self::assertSame('datetime_immutable', $options['input']);
        self::assertFalse($options['grouping']);
        self::assertSame(0, $options['scale']);
    }

    public function testBuildFormAddsTransformer(): void
    {
        $type = new TimestampType();
        $builder = $this->createMock(FormBuilderInterface::class);

        $builder
            ->expects($this->once())
            ->method('addModelTransformer')
            ->with($this->callback(static fn($transformer) => $transformer instanceof DateTimeToNumberTransformer));

        $type->buildForm($builder, ['input' => 'datetime_immutable']);
    }

    public function testParentIsNumberType(): void
    {
        $type = new TimestampType();

        self::assertSame(NumberType::class, $type->getParent());
    }
}
