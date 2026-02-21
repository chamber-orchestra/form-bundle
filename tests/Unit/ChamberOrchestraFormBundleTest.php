<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
