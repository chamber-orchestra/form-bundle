<?php

declare(strict_types=1);

namespace Tests\Integrational;

use ChamberOrchestra\FormBundle\Type\HiddenEntityType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Tests\Integrational\Entity\TestUser;

final class HiddenEntityTypeIntegrationTest extends KernelTestCase
{
    public function testTransformsEntityToIdAndBackWithDoctrine(): void
    {
        if (!class_exists(\Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class)) {
            $this->markTestSkipped('doctrine/doctrine-bundle is required for this integration test.');
        }

        self::bootKernel();
        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $factory = $container->get(FormFactoryInterface::class);

        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema([$em->getClassMetadata(TestUser::class)]);

        $user = new TestUser('user@example.com');
        $em->persist($user);
        $em->flush();

        $form = $factory->create(HiddenEntityType::class, $user, [
            'class' => TestUser::class,
            'data_class' => null,
        ]);

        self::assertSame((string)$user->id, $form->getViewData());

        $submitForm = $factory->create(HiddenEntityType::class, null, [
            'class' => TestUser::class,
            'data_class' => null,
        ]);
        $submitForm->submit((string)$user->id);

        self::assertSame($user->id, $submitForm->getData()?->id);
    }
}
