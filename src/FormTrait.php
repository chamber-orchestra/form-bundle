<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle;

use ChamberOrchestra\FormBundle\Exception\SubmittedFormRequiredException;
use ChamberOrchestra\FormBundle\Exception\XmlHttpRequestRequiredException;
use ChamberOrchestra\FormBundle\View\FailureView;
use ChamberOrchestra\FormBundle\View\RedirectView;
use ChamberOrchestra\FormBundle\View\SuccessHtmlView;
use ChamberOrchestra\FormBundle\View\ValidationFailedView;
use ChamberOrchestra\FormBundle\View\ViolationView;
use ChamberOrchestra\ViewBundle\View\DataView;
use ChamberOrchestra\ViewBundle\View\ResponseView;
use ChamberOrchestra\ViewBundle\View\ViewInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @mixin AbstractController
 */
trait FormTrait
{
    protected function createSuccessResponse(array $data = []): DataView|ResponseView
    {
        return $data ? new DataView($data) : new ResponseView();
    }

    protected function createRedirectResponse(
        string $url,
        int $status = Response::HTTP_MOVED_PERMANENTLY
    ): Response|RedirectView {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        if ($request->isXmlHttpRequest()) {
            return new RedirectView($url, $status);
        }

        return $this->redirect($url, $status);
    }

    protected function createRedirectToRouteResponse(
        string $name,
        array $parameters = [],
        int $status = Response::HTTP_MOVED_PERMANENTLY
    ): Response|RedirectView {
        return $this->createRedirectResponse($this->generateUrl($name, $parameters), $status);
    }

    protected function createExceptionResponse(): FailureView
    {
        return $this->createFailureResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    protected function createSuccessHtmlResponse(string $view, array $parameters = []): Response|SuccessHtmlView
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        if ($request->isXmlHttpRequest()) {
            return new SuccessHtmlView([
                'html' => $this->renderView($view, $parameters),
            ]);
        }

        return $this->render($view, $parameters);
    }

    protected function createFailureResponse(int $status = Response::HTTP_BAD_REQUEST): FailureView
    {
        return new FailureView($status);
    }

    protected function createValidationFailedResponse(FormInterface $form): ValidationFailedView
    {
        return new ValidationFailedView($this->serializeFormErrors($form));
    }

    protected function handleFormCall(
        FormInterface|string $form,
        callable|null $callable = null
    ): Response|ViewInterface {
        if (!\is_string($form) && !$form instanceof FormInterface) {
            throw new \TypeError(
                \sprintf(
                    'Passed $form must be of type "%s", "%s" given.',
                    \implode(',', ['string', FormInterface::class]),
                    \get_debug_type($form)
                )
            );
        }

        if (\is_string($form)) {
            $form = $this->container->get('form.factory')->create($form);
        }

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            throw $this->createSubmittedFormRequiredException(\get_class($form));
        }

        return $this->createSubmittedFormResponse($form, $callable);
    }

    protected function createSubmittedFormResponse(
        FormInterface $form,
        callable|null $callable = null
    ): Response|ViewInterface {
        return $this->onFormSubmitted($form, $callable);
    }

    /**
     * @param null|callable $callable must return @see \ChamberOrchestra\ViewBundle\View\ViewInterface, array or null
     */
    protected function onFormSubmitted(FormInterface $form, callable|null $callable = null): Response|ViewInterface
    {
        if (!$form->isValid()) {
            return $this->createValidationFailedResponse($form);
        }

        if (null === $callable || null === $response = \call_user_func($callable, $form->getData())) {
            return $this->createSuccessResponse();
        }

        if (!\is_array($response) && !$response instanceof ViewInterface && !$response instanceof Response) {
            throw new \TypeError(
                \sprintf(
                    'Passed closure must return %s, returned %s',
                    \implode('|', [ViewInterface::class, Response::class, 'array']),
                    \get_debug_type($response)
                )
            );
        }

        return \is_array($response) ? $this->createSuccessResponse($response) : $response;
    }

    protected function serializeFormErrors(FormInterface $form): array
    {
        return $this->serialiseErrors($form->getErrors(true, false));
    }

    protected function createNotXmlHttpRequestException(): XmlHttpRequestRequiredException
    {
        return new XmlHttpRequestRequiredException();
    }

    protected function createSubmittedFormRequiredException(string $type): SubmittedFormRequiredException
    {
        return new SubmittedFormRequiredException($type);
    }

    private function serialiseErrors(FormErrorIterator $iterator, array $paths = []): array
    {
        if ('' !== $name = $iterator->getForm()->getName()) {
            $paths[] = $name;
        }
        $id = \implode('_', $paths);
        $path = \implode('.', $paths);

        $violations = [];
        foreach ($iterator as $formErrorIterator) {
            if ($formErrorIterator instanceof FormErrorIterator) {
                $violations = \array_merge($violations, $this->serialiseErrors($formErrorIterator, $paths));
                continue;
            }

            /* @var FormError $formErrorIterator */
            $violationEntry = new ViolationView(
                $id,
                $formErrorIterator->getMessage(),
                $formErrorIterator->getMessageParameters(),
                $path
            );

            $cause = $formErrorIterator->getCause();
            if ($cause instanceof ConstraintViolation) {
                if (null !== $code = $cause->getCode()) {
                    $violationEntry->type = \sprintf('urn:uuid:%s', $code);
                }
            }
            $violations[] = $violationEntry;
        }

        return $violations;
    }
}
