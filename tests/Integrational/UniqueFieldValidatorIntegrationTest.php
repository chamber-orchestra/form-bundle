<?php

declare(strict_types=1);

namespace Tests\Integrational;

use ChamberOrchestra\FormBundle\Validator\Constraints\UniqueField;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\Integrational\Entity\TestUser;

final class UniqueFieldValidatorIntegrationTest extends KernelTestCase
{
    public function testUniqueFieldValidatorDetectsDuplicates(): void
    {
        if (!class_exists(\Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class)) {
            $this->markTestSkipped('doctrine/doctrine-bundle is required for this integration test.');
        }

        self::bootKernel();
        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $validator = $container->get(ValidatorInterface::class);

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema([$em->getClassMetadata(TestUser::class)]);
        $schemaTool->createSchema([$em->getClassMetadata(TestUser::class)]);

        $user = new TestUser('user@example.com');
        $em->persist($user);
        $em->flush();

        $constraint = new UniqueField();
        $constraint->entityClass = TestUser::class;
        $constraint->fields = ['email'];

        $violations = $validator->validate('user@example.com', $constraint);
        $noViolations = $validator->validate('unique@example.com', $constraint);

        self::assertCount(1, $violations);
        self::assertSame(UniqueField::ALREADY_USED_ERROR, $violations[0]->getCode());
        self::assertCount(0, $noViolations);
    }
}
