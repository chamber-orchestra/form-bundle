<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle;

use ChamberOrchestra\FormBundle\Type\Api\MutationForm;
use ChamberOrchestra\ViewBundle\View\ViewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait ApiFormTrait
{
    use FormTrait;

    protected function handleApiCall(FormInterface|string $form, callable|null $callable = null): ViewInterface
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        if (!$form instanceof FormInterface && \is_string($form)) {
            $form = $this->container->get('form.factory')->create($form);
        }

        return $this->onFormSubmitted(
            $form->getConfig()->getType()->getInnerType() instanceof MutationForm
                ? $form->submit($this->convertRequestToArray($request))
                : $form->handleRequest($request),
            $callable
        );
    }

    private function convertRequestToArray(Request $request): array
    {
        $data = [];
        if ('json' === $request->getContentTypeFormat() && $request->getContent()) {
            try {
                $data = $request->toArray();
            } catch (\Throwable $e) {
                throw new BadRequestHttpException('Could not convert content into valid JSON.', $e);
            }
        }

        return \array_replace_recursive($data, $request->files->all());
    }
}