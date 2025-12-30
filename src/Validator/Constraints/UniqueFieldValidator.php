<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\Validator\Constraints;

use ChamberOrchestra\FormBundle\Exception\LogicException;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueFieldValidator extends ConstraintValidator
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueField) {
            throw new UnexpectedTypeException($constraint, UniqueField::class);
        }

        if (null === $value || ('' === $value && $constraint->allowEmptyString)) {
            return;
        }

        $normalized = $value;
        if (null !== $constraint->normalizer) {
            $normalized = \call_user_func($constraint->normalizer, $value);
        }

        $criteria = $this->buildCriteria($constraint, $normalized, $value);
        $results = $this->getRepository($constraint)->matching($criteria);
        $resultsCount = $results->count();
        if (0 === $resultsCount) {
            return;
        }

        $this->addViolation($constraint, $value);
    }

    private function addViolation(UniqueField $constraint, $value): void
    {
        $builder = $this->context->buildViolation($constraint->message);
        if ($constraint->errorPath) {
            $builder->atPath($constraint->errorPath);
        }

        if (!\is_array($value) && !\is_resource($value)
            && (!\is_object($value) || $value instanceof \DateTimeInterface || \method_exists($value, '__toString'))) {
            $builder->setParameter(
                '{{ value }}',
                $this->formatValue($value, self::PRETTY_DATE & self::OBJECT_TO_STRING)
            );
        }
        $builder
            ->setCode(UniqueField::ALREADY_USED_ERROR)
            ->setInvalidValue($value)
            ->addViolation();
    }

    private function getRepository(UniqueField $constraint): Selectable
    {
        $manager = $this->getManager($constraint);
        $repository = $manager->getRepository($constraint->entityClass);

        if (!$repository instanceof Selectable) {
            throw new LogicException(
                \sprintf(
                    '%s does not implement %s which is required for Unique validation',
                    \get_class($repository),
                    Selectable::class
                )
            );
        }

        return $repository;
    }

    private function getManager(UniqueField $constraint): ObjectManager
    {
        if (null !== $constraint->em) {
            return $this->doctrine->getManager($constraint->em);
        }

        $em = $this->doctrine->getManagerForClass($constraint->entityClass);
        if (null === $em) {
            throw new ConstraintDefinitionException(
                sprintf('Class "%s" is not managed by doctrine', $constraint->entityClass)
            );
        }

        return $em;
    }

    private function buildComparison(string $field, $value, bool $negative = false): Comparison
    {
        $operation = $negative ? Comparison::NEQ : Comparison::EQ;

        return new Comparison($field, $operation, new Value($value));
    }

    private function addComparisonToCriteria(
        Criteria $criteria,
        Comparison $comparison,
        string $type = CompositeExpression::TYPE_AND
    ): void {
        if (CompositeExpression::TYPE_AND === $type) {
            $criteria->andWhere($comparison);

            return;
        }

        $criteria->orWhere($comparison);
    }

    private function buildCriteria(UniqueField $constraint, $value, $origin): Criteria
    {
        $criteria = Criteria::create();

        // build includes fields with OR join
        foreach ($constraint->fields as $field) {
            $this->addComparisonToCriteria(
                $criteria,
                $this->buildComparison($field, $value),
                CompositeExpression::TYPE_OR
            );
        }

        // build exclude fields with AND join
        $exclude = \is_callable($constraint->exclude)
            ? \call_user_func($constraint->exclude, $origin, $value)
            : $constraint->exclude;

        if (!\is_array($exclude)) {
            throw new ConstraintDefinitionException('Constraint `exclude` as callable must return array.');
        }

        if (\count($exclude)) {
            foreach ($exclude as $field => $param) {
                $this->addComparisonToCriteria(
                    $criteria,
                    $this->buildComparison($field, $param, true),
                    CompositeExpression::TYPE_AND
                );
            }
        }

        return $criteria;
    }
}
