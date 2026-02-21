<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Type\Api;

use ChamberOrchestra\FormBundle\Type\Api\GetForm;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GetFormTest extends TestCase
{
    public function testConfigureOptionsSetsMethod(): void
    {
        $form = new GetForm();
        $resolver = new OptionsResolver();

        $form->configureOptions($resolver);
        $options = $resolver->resolve();

        self::assertSame('GET', $options['method']);
    }

    public function testBlockPrefixIsEmpty(): void
    {
        $form = new GetForm();

        self::assertSame('', $form->getBlockPrefix());
    }
}
