<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Integrational;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormFactoryInterface;

final class TelExtensionIntegrationTest extends TestCase
{
    public function testTelExtensionNormalizesInput(): void
    {
        $kernel = new TestKernel('test', true);
        $kernel->boot();

        $factory = $kernel->getContainer()->get(FormFactoryInterface::class);
        $form = $factory->create(TelType::class, null, ['data_class' => null]);
        $form->submit('1 (2)3');

        $kernel->shutdown();

        self::assertSame('123', $form->getData());
    }
}
