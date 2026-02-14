<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\View;

use ChamberOrchestra\ViewBundle\View\ResponseView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FailureView extends ResponseView
{
    protected string $type = 'https://datatracker.ietf.org/doc/html/rfc9110#section-15';
    protected readonly string $title;

    public function __construct(int $status = JsonResponse::HTTP_BAD_REQUEST, string $message = 'Validation Failed')
    {
        $this->title = $message;
        parent::__construct($status, ['Content-Type' => 'application/problem+json']);
    }

    public function normalize(
        NormalizerInterface $normalizer,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool {
        return $normalizer->normalize([
            'type' => $this->type,
            'title' => $this->title,
        ], $format, $context);
    }
}