<?php

declare(strict_types=1);

namespace Tests\Integrational;

use ChamberOrchestra\FormBundle\Type\HiddenEntityType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Tests\Integrational\Entity\TestUser;

final class HiddenEntityTypeQueryBuilderIntegrationTest extends TestCase
{
    public function testQueryBuilderAndChoiceValueAreUsed(): void
    {
        if (!class_exists(\Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class)) {
            $this->markTestSkipped('doctrine/doctrine-bundle is required for this integration test.');
        }

        $kernel = new TestKernel('test', true);
        $kernel->boot();

        $container = $kernel->getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        $factory = $container->get(FormFactoryInterface::class);

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema([$em->getClassMetadata(TestUser::class)]);
        $schemaTool->createSchema([$em->getClassMetadata(TestUser::class)]);

        $user = new TestUser('user@example.com');
        $em->persist($user);
        $em->flush();

        $form = $factory->create(HiddenEntityType::class, null, [
            'class' => TestUser::class,
            'choice_value' => 'email',
            'data_class' => null,
            'query_builder' => static function (EntityRepository $repository) {
                return $repository->createQueryBuilder('u');
            },
        ]);

        $form->submit('user@example.com');

        $kernel->shutdown();

        self::assertSame($user->id, $form->getData()?->id);
    }
}
