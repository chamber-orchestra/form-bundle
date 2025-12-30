<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\Type\Api;

use Symfony\Component\Form\AbstractType;

abstract class QueryForm extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return '';
    }

    public function getParent(): string
    {
        return GetForm::class;
    }
}
