<?php

declare(strict_types=1);

namespace Tests\Unit\Type\Api;

use ChamberOrchestra\FormBundle\Type\Api\GetForm;
use ChamberOrchestra\FormBundle\Type\Api\QueryForm;
use PHPUnit\Framework\TestCase;

final class QueryFormTest extends TestCase
{
    public function testParentAndBlockPrefix(): void
    {
        $form = new class() extends QueryForm {};

        self::assertSame('', $form->getBlockPrefix());
        self::assertSame(GetForm::class, $form->getParent());
    }
}
