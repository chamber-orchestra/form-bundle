<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Integrational;

use ChamberOrchestra\ViewBundle\ChamberOrchestraViewBundle;
use ChamberOrchestra\FormBundle\ChamberOrchestraFormBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Form\FormFactoryInterface;

final class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        $bundles = [
            new FrameworkBundle(),
            new ChamberOrchestraViewBundle(),
            new ChamberOrchestraFormBundle(),
        ];

        if (\class_exists(DoctrineBundle::class)) {
            $bundles[] = new DoctrineBundle();
        }

        return $bundles;
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'secret' => 'test_secret',
            'test' => true,
            'form' => true,
            'serializer' => ['enabled' => true],
        ]);
        $container->extension('chamber_orchestra_form', []);

        $container->services()
            ->alias(FormFactoryInterface::class, 'form.factory')
            ->public();
        $container->services()
            ->alias(EntityManagerInterface::class, 'doctrine.orm.entity_manager')
            ->public();

        if (\class_exists(DoctrineBundle::class)) {
            $container->extension('doctrine', [
                'dbal' => [
                    'driver' => 'pdo_sqlite',
                    'memory' => true,
                ],
                'orm' => [
                    'entity_managers' => [
                        'default' => [
                            'mappings' => [
                                'Tests' => [
                                    'type' => 'attribute',
                                    'dir' => '%kernel.project_dir%/tests/Integrational/Entity',
                                    'prefix' => 'Tests\\Integrational\\Entity',
                                    'alias' => 'Tests',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        } else {
            $container->services()
                ->set(EntityManagerInterface::class)
                ->synthetic();
            $container->services()
                ->set(ManagerRegistry::class)
                ->synthetic();
        }
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 2);
    }
}
