<?php

declare(strict_types=1);

namespace Tests\Unit\Extension;

use ChamberOrchestra\FormBundle\Extension\TelExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;

final class TelExtensionTest extends TestCase
{
    public function testExtendedTypes(): void
    {
        self::assertSame([TelType::class], iterator_to_array(TelExtension::getExtendedTypes()));
    }

    public function testBuildFormAddsTransformer(): void
    {
        $extension = new TelExtension();
        $builder = $this->createMock(FormBuilderInterface::class);
        $captured = null;

        $builder
            ->expects($this->once())
            ->method('addViewTransformer')
            ->with($this->callback(static function ($transformer) use (&$captured): bool {
                $captured = $transformer;

                return $transformer instanceof CallbackTransformer;
            }));

        $extension->buildForm($builder, []);

        self::assertSame('123', $captured->reverseTransform('1 (2)3'));
        self::assertNull($captured->reverseTransform(''));
    }
}
