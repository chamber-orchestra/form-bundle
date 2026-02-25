<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChamberOrchestra\FormBundle\Extension\TelExtension;
use ChamberOrchestra\FormBundle\Serializer\Normalizer\ProblemNormalizer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->load('ChamberOrchestra\\FormBundle\\', __DIR__.'/../../')
        ->exclude([
            __DIR__.'/../../DependencyInjection/',
            __DIR__.'/../../Resources/',
            __DIR__.'/../../Exception/',
            __DIR__.'/../../Transformer/',
            __DIR__.'/../../Utils/',
            __DIR__.'/../../View/',
        ]);

    $services
        ->set(ProblemNormalizer::class)
        ->arg('$debug', '%kernel.debug%')
        ->public();

    $services
        ->set(TelExtension::class)
        ->tag('form.type_extension');
};
