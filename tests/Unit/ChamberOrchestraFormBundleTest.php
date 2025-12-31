<?php

declare(strict_types=1);

namespace Tests\Unit;

use ChamberOrchestra\FormBundle\ChamberOrchestraFormBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ChamberOrchestraFormBundleTest extends TestCase
{
    public function testExtendsBundle(): void
    {
        $bundle = new ChamberOrchestraFormBundle();

        self::assertInstanceOf(Bundle::class, $bundle);
    }
}
