<?php

declare(strict_types=1);

namespace Tests\Unit\Type;

use ChamberOrchestra\FormBundle\Type\HiddenEntityType;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;

final class HiddenEntityTypeEndToEndTest extends TestCase
{
    public function testTransformsEntityToIdAndBack(): void
    {
        $entity = new EndToEndEntity(1);
        $repository = new InMemoryRepository([$entity]);

        $metadata = $this->createStub(ClassMetadata::class);
        $metadata->method('getSingleIdentifierFieldName')->willReturn('id');
        $metadata->method('hasField')->willReturn(true);
        $metadata->method('getFieldValue')->willReturnCallback(
            static fn(object $value, string $field) => $value->{$field}
        );

        $em = $this->createStub(EntityManagerInterface::class);
        $em->method('getClassMetadata')->willReturn($metadata);
        $em->method('getRepository')->willReturn($repository);

        $factory = Forms::createFormFactoryBuilder()
            ->addType(new HiddenEntityType($em))
            ->getFormFactory();

        $form = $factory->create(HiddenEntityType::class, $entity, [
            'class' => EndToEndEntity::class,
            'data_class' => null,
        ]);

        self::assertInstanceOf(FormInterface::class, $form);
        self::assertSame('1', $form->getViewData());

        $submitForm = $factory->create(HiddenEntityType::class, null, [
            'class' => EndToEndEntity::class,
            'data_class' => null,
        ]);
        $submitForm->submit('1');

        self::assertSame($entity, $submitForm->getData());
    }
}

final class EndToEndEntity
{
    public function __construct(public int $id)
    {
    }
}

final class InMemoryRepository extends EntityRepository
{
    /** @var array<int, object> */
    private array $items;

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(array $items)
    {
        $this->items = [];
        foreach ($items as $item) {
            $this->items[$item->id] = $item;
        }
    }

    public function find(mixed $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null): ?object
    {
        return $this->items[(int)$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->items);
    }

    public function findBy(array $criteria, array|null $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return [];
    }

    public function findOneBy(array $criteria, array|null $orderBy = null): ?object
    {
        $id = (int)($criteria['id'] ?? 0);

        return $this->items[$id] ?? null;
    }

    public function getClassName(): string
    {
        return EndToEndEntity::class;
    }
}
