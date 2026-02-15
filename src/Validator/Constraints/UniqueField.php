<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueField extends Constraint
{
    public const string ALREADY_USED_ERROR = 'a72be866-aae8-4be7-ac1d-fa4f73c167aa';
    public string $message = 'This value has been already used.';
    public string|null $em = null;
    public string|null $entityClass = null;
    /**
     * @var array OR condition
     */
    public array $fields = [];
    /**
     * @var array<string, mixed>|\Closure AND condition
     */
    public array|\Closure $exclude = [];
    public string|null $errorPath = null;
    public ?\Closure $normalizer = null;
    public bool $allowEmptyString = false;
    protected const array ERROR_NAMES = [
        self::ALREADY_USED_ERROR => 'ALREADY_USED_ERROR',
    ];

    public function __construct(?array $options = null)
    {
        parent::__construct($options);
    }

    public function getTargets(): array
    {
        return [self::PROPERTY_CONSTRAINT];
    }
}
