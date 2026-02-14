<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\Type;

use ChamberOrchestra\FormBundle\Exception\InvalidArgumentException;
use ChamberOrchestra\FormBundle\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HiddenEntityType extends AbstractType
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $em = $this->em;
        $builder->addViewTransformer(
            new CallbackTransformer(
                function (object|null $value) use ($options, $em): string|null {
                    if (null === $value) {
                        return null;
                    }

                    $class = $em->getClassMetadata($options['class']);
                    $id = $class->getFieldValue($value, $options['choice_value']);

                    if (!\is_scalar($id) && (!\is_object($id) || !\method_exists($id, '__toString'))) {
                        throw TransformationFailedException::notAllowedType($value, ['scalar', 'string']);
                    }

                    return (string)$id;
                },
                function (mixed $id) use ($options, $em): object|null {
                    if (!\is_scalar($id)) {
                        throw TransformationFailedException::notAllowedType($id, ['scalar']);
                    }

                    if ($id === null || $id === false || $id === '') {
                        return null;
                    }

                    if (null !== $options['query_builder']) {
                        $qb = $this->prepareQueryBuilder($options['query_builder'], $options['choice_value'], $id);
                        $entity = $qb->getQuery()->getOneOrNullResult();
                    } else {
                        $er = $em->getRepository($options['class']);
                        $entity = $er->findOneBy([$options['choice_value'] => (string)$id]);
                    }

                    if (null === $entity) {
                        throw new TransformationFailedException(
                            \sprintf("Object of class '%s' was not found.", $options['class'])
                        );
                    }

                    return $entity;
                }
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => null,
            'query_builder' => null,
            'compound' => false,
            'choice_value' => null,
        ]);

        $em = $this->em;

        $resolver
            ->setRequired('class')
            ->setAllowedTypes('class', 'string')
            ->setAllowedValues('class', function ($value) use ($em): bool {
                if (!\class_exists($value)) {
                    return false;
                }

                try {
                    $em->getClassMetadata($value);
                    return true;
                } catch (\Throwable) {
                    return false;
                }
            });

        $resolver
            ->setAllowedTypes('query_builder', ['null', 'callable', QueryBuilder::class])
            ->setNormalizer('query_builder', function (Options $options, $value) use ($em): ?QueryBuilder {
                if (null === $value || $value instanceof QueryBuilder) {
                    return $value;
                }

                /** @var EntityRepository $er */
                $er = $em->getRepository($options['class']);
                $qb = \call_user_func($value, $er);

                if (!$qb instanceof QueryBuilder) {
                    throw new InvalidArgumentException(
                        \sprintf(
                            'Parameter "query_builder" must return instance of "%s", "%s" returned.',
                            QueryBuilder::class,
                            \get_debug_type($qb)
                        )
                    );
                }

                return $qb;
            });

        $resolver
            ->setAllowedTypes('choice_value', ['null', 'string'])
            ->setNormalizer('choice_value', function (Options $options, $value) use ($em) {
                $class = $em->getClassMetadata($options['class']);

                if (null === $value) {
                    return $class->getSingleIdentifierFieldName();
                }

                if (!$class->hasField($value)) {
                    throw new InvalidArgumentException(
                        \sprintf('Class "%s" does not have field with name "%s".', $options['class'], $value)
                    );
                }

                return $value;
            });
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }

    private function prepareQueryBuilder(QueryBuilder $qb, string $idFieldName, mixed $id): QueryBuilder
    {
        if (!\preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $idFieldName)) {
            throw new InvalidArgumentException(
                \sprintf('Invalid field name "%s".', $idFieldName)
            );
        }

        $aliases = $qb->getRootAliases();
        if (empty($aliases)) {
            throw new InvalidArgumentException('QueryBuilder must have at least one root alias.');
        }

        $alias = $aliases[0];
        $param = 'param_'.\bin2hex(\random_bytes(8));

        $qb
            ->andWhere($qb->expr()->eq(\sprintf('%s.%s', $alias, $idFieldName), ':'.$param))
            ->setParameter($param, $id);

        return $qb;
    }
}
