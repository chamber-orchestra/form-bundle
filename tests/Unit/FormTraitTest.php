<?php

declare(strict_types=1);

namespace Tests\Unit;

use ChamberOrchestra\FormBundle\FormTrait;
use ChamberOrchestra\FormBundle\View\ValidationFailedView;
use ChamberOrchestra\FormBundle\View\ViolationView;
use ChamberOrchestra\ViewBundle\View\DataView;
use ChamberOrchestra\ViewBundle\View\ResponseView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;

final class FormTraitTest extends TestCase
{
    public function testCreateSuccessResponseReturnsDataViewWhenDataProvided(): void
    {
        $host = new class() {
            use FormTrait;

            public function exposeCreateSuccessResponse(array $data = []): DataView|ResponseView
            {
                return $this->createSuccessResponse($data);
            }
        };

        $response = $host->exposeCreateSuccessResponse(['ok' => true]);

        self::assertInstanceOf(DataView::class, $response);
    }

    public function testCreateSuccessResponseReturnsResponseViewWhenEmpty(): void
    {
        $host = new class() {
            use FormTrait;

            public function exposeCreateSuccessResponse(array $data = []): DataView|ResponseView
            {
                return $this->createSuccessResponse($data);
            }
        };

        $response = $host->exposeCreateSuccessResponse();

        self::assertInstanceOf(ResponseView::class, $response);
    }

    public function testOnFormSubmittedReturnsValidationFailedViewWhenInvalid(): void
    {
        $host = new class() {
            use FormTrait;

            public function exposeOnFormSubmitted(FormInterface $form, ?callable $callable = null)
            {
                return $this->onFormSubmitted($form, $callable);
            }

            protected function createValidationFailedResponse(FormInterface $form): ValidationFailedView
            {
                return new ValidationFailedView([]);
            }
        };

        $form = $this->createStub(FormInterface::class);
        $form->method('isValid')->willReturn(false);

        $response = $host->exposeOnFormSubmitted($form);

        self::assertInstanceOf(ValidationFailedView::class, $response);
    }

    public function testOnFormSubmittedReturnsDataViewFromCallable(): void
    {
        $host = new class() {
            use FormTrait;

            public function exposeOnFormSubmitted(FormInterface $form, ?callable $callable = null)
            {
                return $this->onFormSubmitted($form, $callable);
            }
        };

        $form = $this->createStub(FormInterface::class);
        $form->method('isValid')->willReturn(true);
        $form->method('getData')->willReturn(['id' => 1]);

        $response = $host->exposeOnFormSubmitted($form, fn() => ['ok' => true]);

        self::assertInstanceOf(DataView::class, $response);
    }

    public function testSerializeFormErrorsBuildsViolationStructure(): void
    {
        $host = new class() {
            use FormTrait;

            public function exposeSerializeFormErrors(FormInterface $form): array
            {
                return $this->serializeFormErrors($form);
            }
        };

        $builder = Forms::createFormFactory()->createNamedBuilder('root', FormType::class, null, [
            'data_class' => null,
        ]);
        $builder->add('email');
        $form = $builder->getForm();

        $violation = new ConstraintViolation(
            'Invalid email',
            'Invalid email',
            ['{{ value }}' => 'invalid'],
            null,
            'email',
            'invalid',
            null,
            '6b8f5b7a-2d83-4c7a-9e16-1b9d7e48ab9c'
        );

        $form->get('email')->addError(
            new FormError('Invalid email', 'Invalid email', ['{{ value }}' => 'invalid'], null, $violation)
        );

        $violations = $host->exposeSerializeFormErrors($form);

        self::assertCount(1, $violations);
        self::assertInstanceOf(ViolationView::class, $violations[0]);
        self::assertSame('root_email', $violations[0]->id);
        self::assertSame('Invalid email', $violations[0]->title);
        self::assertSame(['{{ value }}' => 'invalid'], $violations[0]->parameters);
        self::assertSame('root.email', $violations[0]->propertyPath);
        self::assertSame('urn:uuid:6b8f5b7a-2d83-4c7a-9e16-1b9d7e48ab9c', $violations[0]->type);
    }

    public function testSerializeFormErrorsBuildsViolationStructureForEmbeddedForm(): void
    {
        $host = new class() {
            use FormTrait;

            public function exposeSerializeFormErrors(FormInterface $form): array
            {
                return $this->serializeFormErrors($form);
            }
        };

        $factory = Forms::createFormFactory();
        $builder = $factory->createNamedBuilder('root', FormType::class, null, [
            'data_class' => null,
        ]);
        $builder->add('profile', FormType::class, ['data_class' => null]);
        $builder->get('profile')->add('email');
        $form = $builder->getForm();

        $violation = new ConstraintViolation(
            'Invalid email',
            'Invalid email',
            ['{{ value }}' => 'invalid'],
            null,
            'email',
            'invalid',
            null,
            '3f238b83-2c8a-4f58-b7a6-3d54a3bd93ed'
        );

        $form->get('profile')->get('email')->addError(
            new FormError('Invalid email', 'Invalid email', ['{{ value }}' => 'invalid'], null, $violation)
        );

        $violations = $host->exposeSerializeFormErrors($form);

        self::assertCount(1, $violations);
        self::assertInstanceOf(ViolationView::class, $violations[0]);
        self::assertSame('root_profile_email', $violations[0]->id);
        self::assertSame('root.profile.email', $violations[0]->propertyPath);
        self::assertSame('urn:uuid:3f238b83-2c8a-4f58-b7a6-3d54a3bd93ed', $violations[0]->type);
        self::assertSame(['{{ value }}' => 'invalid'], $violations[0]->parameters);
    }

    public function testCreateRedirectResponseReturnsViewForXmlHttpRequest(): void
    {
        $request = new Request();
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $stack = new RequestStack();
        $stack->push($request);

        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')->willReturnCallback(fn(string $id) => match ($id) {
            'request_stack' => $stack,
        });

        $host = new class($container) {
            use FormTrait;

            protected \Psr\Container\ContainerInterface $container;

            public function __construct(ContainerInterface $container)
            {
                $this->container = $container;
            }

            protected function redirect(string $url, int $status = 302): RedirectResponse
            {
                return new RedirectResponse('', $status, ['Location' => $url]);
            }

            public function exposeCreateRedirectResponse(
                string $url,
                int $status
            ): Response|\ChamberOrchestra\FormBundle\View\RedirectView {
                return $this->createRedirectResponse($url, $status);
            }
        };

        $response = $host->exposeCreateRedirectResponse('/target', Response::HTTP_FOUND);

        self::assertInstanceOf(\ChamberOrchestra\FormBundle\View\RedirectView::class, $response);
        self::assertSame('/target', $response->location);
        self::assertSame(Response::HTTP_FOUND, $response->status);
    }

    public function testCreateRedirectResponseReturnsResponseForNonXmlHttpRequest(): void
    {
        $request = new Request();
        $stack = new RequestStack();
        $stack->push($request);

        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')->willReturnCallback(fn(string $id) => match ($id) {
            'request_stack' => $stack,
        });

        $host = new class($container) {
            use FormTrait;

            protected \Psr\Container\ContainerInterface $container;

            public function __construct(ContainerInterface $container)
            {
                $this->container = $container;
            }

            protected function redirect(string $url, int $status = 302): RedirectResponse
            {
                return new RedirectResponse($url, $status, ['Location' => $url]);
            }

            public function exposeCreateRedirectResponse(string $url, int $status): Response
            {
                return $this->createRedirectResponse($url, $status);
            }
        };

        $response = $host->exposeCreateRedirectResponse('/target', Response::HTTP_FOUND);

        self::assertInstanceOf(Response::class, $response);
        self::assertSame('/target', $response->headers->get('Location'));
        self::assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testCreateSuccessHtmlResponseHandlesXmlHttpRequest(): void
    {
        $request = new Request();
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $stack = new RequestStack();
        $stack->push($request);

        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')->willReturnCallback(fn(string $id) => match ($id) {
            'request_stack' => $stack,
        });

        $host = new class($container) {
            use FormTrait;

            protected \Psr\Container\ContainerInterface $container;

            public function __construct(ContainerInterface $container)
            {
                $this->container = $container;
            }

            public function renderView(string $view, array $parameters = []): string
            {
                return '<p>html</p>';
            }

            protected function render(string $view, array $parameters = [], ?Response $response = null): Response
            {
                return new Response('html');
            }

            public function exposeCreateSuccessHtmlResponse(
                string $view,
                array $parameters = []
            ): Response|\ChamberOrchestra\FormBundle\View\SuccessHtmlView {
                return $this->createSuccessHtmlResponse($view, $parameters);
            }
        };

        $response = $host->exposeCreateSuccessHtmlResponse('template.html.twig');

        self::assertInstanceOf(\ChamberOrchestra\FormBundle\View\SuccessHtmlView::class, $response);
        self::assertSame(['html' => '<p>html</p>'], $response->data);
    }

    public function testCreateSuccessHtmlResponseHandlesNonXmlHttpRequest(): void
    {
        $request = new Request();
        $stack = new RequestStack();
        $stack->push($request);

        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')->willReturnCallback(fn(string $id) => match ($id) {
            'request_stack' => $stack,
        });

        $host = new class($container) {
            use FormTrait;

            protected \Psr\Container\ContainerInterface $container;

            public function __construct(ContainerInterface $container)
            {
                $this->container = $container;
            }

            public function renderView(string $view, array $parameters = []): string
            {
                return '<p>html</p>';
            }

            protected function render(string $view, array $parameters = [], ?Response $response = null): Response
            {
                return new Response('html');
            }

            public function exposeCreateSuccessHtmlResponse(string $view, array $parameters = []): Response
            {
                return $this->createSuccessHtmlResponse($view, $parameters);
            }
        };

        $response = $host->exposeCreateSuccessHtmlResponse('template.html.twig');

        self::assertInstanceOf(Response::class, $response);
        self::assertSame('html', $response->getContent());
    }

    public function testHandleFormCallThrowsOnNullRequest(): void
    {
        $stack = new RequestStack();

        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')->willReturnCallback(function (string $id) use ($stack) {
            return match ($id) {
                'request_stack' => $stack,
                'form.factory' => Forms::createFormFactory(),
            };
        });
        $container->method('has')->willReturn(true);

        $host = new class($container) {
            use FormTrait;

            protected \Psr\Container\ContainerInterface $container;

            public function __construct(ContainerInterface $container)
            {
                $this->container = $container;
            }

            public function exposeHandleFormCall(FormInterface|string $form): mixed
            {
                return $this->handleFormCall($form);
            }
        };

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot handle form call without an active request.');

        $host->exposeHandleFormCall(FormType::class);
    }

    public function testCreateRedirectResponseFallsBackWhenNoRequest(): void
    {
        $stack = new RequestStack();

        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')->willReturnCallback(fn(string $id) => match ($id) {
            'request_stack' => $stack,
        });

        $host = new class($container) {
            use FormTrait;

            protected \Psr\Container\ContainerInterface $container;

            public function __construct(ContainerInterface $container)
            {
                $this->container = $container;
            }

            protected function redirect(string $url, int $status = 302): RedirectResponse
            {
                return new RedirectResponse($url, $status);
            }

            public function exposeCreateRedirectResponse(string $url, int $status): Response
            {
                return $this->createRedirectResponse($url, $status);
            }
        };

        $response = $host->exposeCreateRedirectResponse('/target', Response::HTTP_FOUND);

        self::assertInstanceOf(RedirectResponse::class, $response);
    }
}
