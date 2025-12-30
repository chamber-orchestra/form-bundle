<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;

class TelExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [TelType::class];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer(
            new CallbackTransformer(
                function (string|null $value): string|null {
                    return $value;
                },
                function (string|null $value): string|null {
                    if (null === $value || '' === $value) {
                        return null;
                    }

                    return \preg_replace('/[^\d]/', '', $value);
                }
            )
        );
    }
}