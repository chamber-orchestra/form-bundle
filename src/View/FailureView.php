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
    public string $type = 'https://tools.ietf.org/html/rfc2616#section-10';
    public int $status;
    public string $title;

    public function __construct(int $status = JsonResponse::HTTP_BAD_REQUEST, string $message = 'Validation Failed')
    {
        $this->status = $status;
        $this->title = $message;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return ['Content-Type' => 'application/problem+json'];
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