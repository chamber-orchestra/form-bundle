<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\Type;

use ChamberOrchestra\FormBundle\Transformer\TextToBoolTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends AbstractType<bool> */
class BooleanType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => false,
            'true_values' => [1, '1', true, 'true'],
            'false_values' => [0, '0', false, 'false'],
        ]);
    }

    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var list<mixed> $trueValues */
        $trueValues = $options['true_values'];
        /** @var list<mixed> $falseValues */
        $falseValues = $options['false_values'];
        $builder->addModelTransformer(new TextToBoolTransformer($trueValues, $falseValues));
    }
}
