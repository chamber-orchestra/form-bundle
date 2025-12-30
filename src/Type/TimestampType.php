<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\Type;

use ChamberOrchestra\FormBundle\Transformer\DateTimeToNumberTransformer;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimestampType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'input' => 'datetime_immutable',
            'grouping' => false,
            'scale' => 0,
        ]);

        $resolver->setAllowedValues('input', [
            'datetime',
            'datetime_immutable',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        match ($options['input']) {
            'datetime' => $builder->addModelTransformer(new DateTimeToNumberTransformer(DatePoint::class)),
            'datetime_immutable' => $builder->addModelTransformer(new DateTimeToNumberTransformer(DatePoint::class)),
        };
    }

    public function getParent(): string
    {
        return NumberType::class;
    }
}