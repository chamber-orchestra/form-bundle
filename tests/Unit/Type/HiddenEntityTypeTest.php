<?php

declare(strict_types=1);

namespace Tests\Unit\Type;

use ChamberOrchestra\FormBundle\Exception\InvalidArgumentException;
use ChamberOrchestra\FormBundle\Type\HiddenEntityType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class HiddenEntityTypeTest extends TestCase
{
    public function testConfigureOptionsDefaultsChoiceValueToIdentifier(): void
    {
        $metadata = $this->createStub(ClassMetadata::class);
        $metadata->method('getSingleIdentifierFieldName')->willReturn('id');
        $metadata->method('hasField')->willReturn(true);

        $em = $this->createStub(EntityManagerInterface::class);
        $em->method('getClassMetadata')->willReturn($metadata);

        $type = new HiddenEntityType($em);
        $resolver = new OptionsResolver();

        $type->configureOptions($resolver);
        $options = $resolver->resolve(['class' => DummyEntity::class]);

        self::assertSame('id', $options['choice_value']);
    }

    public function testConfigureOptionsRejectsUnknownChoiceValue(): void
    {
        $metadata = $this->createStub(ClassMetadata::class);
        $metadata->method('getSingleIdentifierFieldName')->willReturn('id');
        $metadata->method('hasField')->willReturn(false);

        $em = $this->createStub(EntityManagerInterface::class);
        $em->method('getClassMetadata')->willReturn($metadata);

        $type = new HiddenEntityType($em);
        $resolver = new OptionsResolver();

        $type->configureOptions($resolver);

        $this->expectException(InvalidArgumentException::class);
        $resolver->resolve(['class' => DummyEntity::class, 'choice_value' => 'missing']);
    }

    public function testBuildFormAddsViewTransformer(): void
    {
        $em = $this->createStub(EntityManagerInterface::class);
        $type = new HiddenEntityType($em);
        $builder = $this->createMock(FormBuilderInterface::class);

        $builder
            ->expects($this->once())
            ->method('addViewTransformer')
            ->with($this->callback(static fn($transformer) => $transformer instanceof CallbackTransformer));

        $type->buildForm($builder, [
            'class' => DummyEntity::class,
            'query_builder' => null,
            'choice_value' => 'id',
        ]);
    }

    public function testParentIsHiddenType(): void
    {
        $em = $this->createStub(EntityManagerInterface::class);
        $type = new HiddenEntityType($em);

        self::assertSame(HiddenType::class, $type->getParent());
    }
}

final class DummyEntity
{
    public function __construct(public int $id = 1)
    {
    }
}
