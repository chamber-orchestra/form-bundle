<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\View;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ValidationFailedView extends FailureView
{
    protected string $type = 'https://symfony.com/errors/validation';
    protected string $detail;
    /** @var list<ViolationView> */
    protected array $violations;

    /** @param list<ViolationView> $violations */
    public function __construct(array $violations = [], string $message = 'Validation Failed')
    {
        $this->detail = \implode("\n", \array_map(static fn (ViolationView $error): string => $error->title, $violations));
        $this->violations = $violations;

        parent::__construct(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $message);
    }

    /** @param array<string, mixed> $context */
    public function normalize(
        NormalizerInterface $normalizer,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool {
        /** @var array<string, mixed> $data */
        $data = $normalizer->normalize([
            'title' => $this->title,
            'type' => $this->type,
            'detail' => $this->detail,
            'violations' => $this->violations,
        ], $format, $context);

        return $data;
    }
}
