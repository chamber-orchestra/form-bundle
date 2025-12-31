<?php

declare(strict_types=1);

namespace Tests\Unit\Type\Api;

use ChamberOrchestra\FormBundle\Type\Api\PostForm;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PostFormTest extends TestCase
{
    public function testConfigureOptionsSetsMethod(): void
    {
        $form = new PostForm();
        $resolver = new OptionsResolver();

        $form->configureOptions($resolver);
        $options = $resolver->resolve();

        self::assertSame('POST', $options['method']);
    }

    public function testBlockPrefixIsEmpty(): void
    {
        $form = new PostForm();

        self::assertSame('', $form->getBlockPrefix());
    }
}
