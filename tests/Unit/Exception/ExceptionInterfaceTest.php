<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Exception;

use ChamberOrchestra\FormBundle\Exception\ExceptionInterface;
use PHPUnit\Framework\TestCase;

final class ExceptionInterfaceTest extends TestCase
{
    public function testInterfaceExists(): void
    {
        self::assertTrue(\interface_exists(ExceptionInterface::class));
    }
}
