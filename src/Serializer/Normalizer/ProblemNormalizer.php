<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\Serializer\Normalizer;

use ChamberOrchestra\FormBundle\Exception\TranslatableExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ProblemNormalizer as Normalizer;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProblemNormalizer extends Normalizer
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        bool $debug = false,
        array $defaultContext = []
    ) {
        parent::__construct($debug, $defaultContext);
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = parent::normalize($object, $format, $context);

        if (($e = $context['exception'] ?? null) instanceof TranslatableExceptionInterface) {
            $data['message'] = $e->getTranslatableMessage()->trans($this->translator);
        }

        return $data;
    }
}