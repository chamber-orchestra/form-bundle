<?php

declare(strict_types=1);

namespace Tests\Unit\Type\Api;

use ChamberOrchestra\FormBundle\Type\Api\GetForm;
use ChamberOrchestra\FormBundle\Type\Api\QueryForm;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class QueryFormTest extends TestCase
{
    public function testParentAndBlockPrefix(): void
    {
        $form = new class() extends QueryForm {};

        self::assertSame('', $form->getBlockPrefix());
        self::assertSame(GetForm::class, $form->getParent());
    }

    public function testCsrfProtectionIsDisabled(): void
    {
        $form = new class() extends QueryForm {};
        $resolver = new OptionsResolver();

        $form->configureOptions($resolver);
        $options = $resolver->resolve([]);

        self::assertFalse($options['csrf_protection']);
    }
}
