<?php

declare(strict_types=1);

namespace Tests\Unit\DependencyInjection;

use ChamberOrchestra\FormBundle\DependencyInjection\ChamberOrchestraFormExtension;
use ChamberOrchestra\FormBundle\Extension\TelExtension;
use ChamberOrchestra\FormBundle\Serializer\Normalizer\ProblemNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ChamberOrchestraFormExtensionTest extends TestCase
{
    public function testLoadsServiceDefinitions(): void
    {
        $container = new ContainerBuilder();
        $extension = new ChamberOrchestraFormExtension();

        $extension->load([], $container);

        self::assertTrue($container->hasDefinition(ProblemNormalizer::class));
        self::assertTrue($container->hasDefinition(TelExtension::class));
    }
}
