<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Integrational;

use ChamberOrchestra\FormBundle\Extension\TelExtension;
use ChamberOrchestra\FormBundle\Serializer\Normalizer\ProblemNormalizer;
use ChamberOrchestra\FormBundle\Validator\Constraints\UniqueFieldValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ServiceWiringTest extends KernelTestCase
{
    public function testContainerHasBundleServices(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        self::assertTrue($container->has(ProblemNormalizer::class));
        self::assertTrue($container->has(TelExtension::class));
        self::assertTrue($container->has(UniqueFieldValidator::class));
    }
}
