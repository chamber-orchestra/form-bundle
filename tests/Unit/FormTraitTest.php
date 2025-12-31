<?php

declare(strict_types=1);

namespace Tests\Unit;

use ChamberOrchestra\FormBundle\FormTrait;
use ChamberOrchestra\FormBundle\View\ValidationFailedView;
use ChamberOrchestra\FormBundle\View\ViolationView;
use ChamberOrchestra\ViewBundle\View\DataView;
use ChamberOrchestra\ViewBundle\View\ResponseView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
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
}
