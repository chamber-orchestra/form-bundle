<?php

declare(strict_types=1);

namespace Tests\Unit\Validator\Constraints;

use ChamberOrchestra\FormBundle\Exception\LogicException;
use ChamberOrchestra\FormBundle\Validator\Constraints\UniqueField;
use ChamberOrchestra\FormBundle\Validator\Constraints\UniqueFieldValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

#[AllowMockObjectsWithoutExpectations]
final class UniqueFieldValidatorTest extends ConstraintValidatorTestCase
{
    private ManagerRegistry&MockObject $registry;
    private ObjectManager&MockObject $objectManager;
    private object $repository;

    protected function setUp(): void
    {
        $this->repository = new SelectableRepository(0);
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->objectManager
            ->method('getRepository')
            ->willReturnCallback(fn() => $this->repository);

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry
            ->method('getManagerForClass')
            ->willReturn($this->objectManager);
        $this->registry
            ->method('getManager')
            ->willReturn($this->objectManager);

        parent::setUp();
    }

    protected function createValidator(): UniqueFieldValidator
    {
        return new UniqueFieldValidator($this->registry);
    }

    public function testUnexpectedConstraintTypeThrows(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('value', new \Symfony\Component\Validator\Constraints\NotBlank());
    }

    public function testNullValueIsIgnored(): void
    {
        $constraint = new UniqueField();
        $constraint->entityClass = DummyEntity::class;
        $constraint->fields = ['email'];

        $this->validator->validate(null, $constraint);

        $this->assertNoViolation();
    }

    public function testViolationRaisedWhenResultExists(): void
    {
        $this->repository = new SelectableRepository(1);

        $constraint = new UniqueField();
        $constraint->entityClass = DummyEntity::class;
        $constraint->fields = ['email'];

        $this->validator->validate('value', $constraint);

        $this
            ->buildViolation($constraint->message)
            ->setParameter('{{ value }}', '"value"')
            ->setCode(UniqueField::ALREADY_USED_ERROR)
            ->setInvalidValue('value')
            ->assertRaised();
    }

    public function testExcludeCallableMustReturnArray(): void
    {
        $constraint = new UniqueField();
        $constraint->entityClass = DummyEntity::class;
        $constraint->fields = ['email'];
        $constraint->exclude = static fn() => 'invalid';

        $this->expectException(ConstraintDefinitionException::class);

        $this->validator->validate('value', $constraint);
    }

    public function testRepositoryMustBeSelectable(): void
    {
        $this->repository = new NonSelectableRepository();

        $constraint = new UniqueField();
        $constraint->entityClass = DummyEntity::class;
        $constraint->fields = ['email'];

        $this->expectException(LogicException::class);

        $this->validator->validate('value', $constraint);
    }
}

final class SelectableRepository implements Selectable, ObjectRepository
{
    public function __construct(private int $count)
    {
    }

    public function matching(Criteria $criteria): ArrayCollection
    {
        return new ArrayCollection(array_fill(0, $this->count, new \stdClass()));
    }

    public function find(mixed $id): ?object
    {
        return null;
    }

    public function findAll(): array
    {
        return [];
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return [];
    }

    public function findOneBy(array $criteria): ?object
    {
        return null;
    }

    public function getClassName(): string
    {
        return DummyEntity::class;
    }
}

final class DummyEntity
{
}

final class NonSelectableRepository implements ObjectRepository
{
    public function find(mixed $id): ?object
    {
        return null;
    }

    public function findAll(): array
    {
        return [];
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return [];
    }

    public function findOneBy(array $criteria): ?object
    {
        return null;
    }

    public function getClassName(): string
    {
        return DummyEntity::class;
    }
}
