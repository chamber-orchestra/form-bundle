<?php

declare(strict_types=1);

namespace Tests\Integrational;

use ChamberOrchestra\FormBundle\Type\BooleanType;
use ChamberOrchestra\FormBundle\Type\TimestampType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;

final class FormTypesIntegrationTest extends TestCase
{
    public function testFormFactoryCreatesBundleTypes(): void
    {
        $factory = Forms::createFormFactoryBuilder()
            ->addType(new BooleanType())
            ->addType(new TimestampType())
            ->getFormFactory();

        $booleanForm = $factory->create(BooleanType::class);
        $timestampForm = $factory->create(TimestampType::class);

        self::assertInstanceOf(FormInterface::class, $booleanForm);
        self::assertInstanceOf(FormInterface::class, $timestampForm);
    }
}
