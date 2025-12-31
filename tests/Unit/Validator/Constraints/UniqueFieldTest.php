<?php

declare(strict_types=1);

namespace Tests\Unit\Validator\Constraints;

use ChamberOrchestra\FormBundle\Validator\Constraints\UniqueField;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;

final class UniqueFieldTest extends TestCase
{
    public function testErrorNameMapping(): void
    {
        self::assertSame('ALREADY_USED_ERROR', UniqueField::getErrorName(UniqueField::ALREADY_USED_ERROR));
    }

    public function testTargetsPropertyConstraint(): void
    {
        $constraint = new UniqueField();

        self::assertSame([Constraint::PROPERTY_CONSTRAINT], $constraint->getTargets());
    }
}
