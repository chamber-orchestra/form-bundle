<?php

declare(strict_types=1);

namespace Tests\Unit\Type\Api;

use ChamberOrchestra\FormBundle\Type\Api\MutationForm;
use ChamberOrchestra\FormBundle\Type\Api\PostForm;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MutationFormTest extends TestCase
{
    public function testParentAndBlockPrefix(): void
    {
        $form = new class() extends MutationForm {};

        self::assertSame('', $form->getBlockPrefix());
        self::assertSame(PostForm::class, $form->getParent());
    }

    public function testCsrfProtectionIsDisabled(): void
    {
        $form = new class() extends MutationForm {};
        $resolver = new OptionsResolver();

        $form->configureOptions($resolver);
        $options = $resolver->resolve([]);

        self::assertFalse($options['csrf_protection']);
    }
}
