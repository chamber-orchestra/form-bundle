<?php

declare(strict_types=1);

namespace Tests\Unit\Type;

use ChamberOrchestra\FormBundle\Transformer\TextToBoolTransformer;
use ChamberOrchestra\FormBundle\Type\BooleanType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BooleanTypeTest extends TestCase
{
    public function testConfigureOptionsSetsDefaults(): void
    {
        $type = new BooleanType();
        $resolver = new OptionsResolver();

        $type->configureOptions($resolver);
        $options = $resolver->resolve();

        self::assertFalse($options['compound']);
        self::assertSame([1, '1', true, 'true'], $options['true_values']);
        self::assertSame([0, '0', false, 'false'], $options['false_values']);
    }

    public function testBuildFormAddsTransformer(): void
    {
        $type = new BooleanType();
        $builder = $this->createMock(FormBuilderInterface::class);

        $builder
            ->expects($this->once())
            ->method('addModelTransformer')
            ->with($this->callback(static fn($transformer) => $transformer instanceof TextToBoolTransformer));

        $type->buildForm($builder, [
            'true_values' => [1],
            'false_values' => [0],
        ]);
    }
}
