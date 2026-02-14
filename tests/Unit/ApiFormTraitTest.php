<?php

declare(strict_types=1);

namespace Tests\Unit;

use ChamberOrchestra\FormBundle\ApiFormTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ApiFormTraitTest extends TestCase
{
    public function testConvertRequestToArrayMergesJsonAndFiles(): void
    {
        $host = new class() {
            use ApiFormTrait;

            public function exposeConvertRequestToArray(Request $request): array
            {
                return $this->convertRequestToArray($request);
            }
        };

        $request = Request::create(
            '/test',
            'POST',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['payload' => ['id' => 1]], JSON_THROW_ON_ERROR)
        );
        $request->files->set('file', ['name' => 'upload.txt']);

        $result = $host->exposeConvertRequestToArray($request);

        self::assertSame(['payload' => ['id' => 1], 'file' => ['name' => 'upload.txt']], $result);
    }

    public function testConvertRequestToArrayThrowsOnInvalidJson(): void
    {
        $host = new class() {
            use ApiFormTrait;

            public function exposeConvertRequestToArray(Request $request): array
            {
                return $this->convertRequestToArray($request);
            }
        };

        $request = Request::create(
            '/test',
            'POST',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{'
        );

        $this->expectException(BadRequestHttpException::class);

        $host->exposeConvertRequestToArray($request);
    }

    public function testConvertRequestToArrayWithFilesOnly(): void
    {
        $host = new class() {
            use ApiFormTrait;

            public function exposeConvertRequestToArray(Request $request): array
            {
                return $this->convertRequestToArray($request);
            }
        };

        $request = Request::create('/test', 'POST');
        $request->files->set('file', ['name' => 'upload.txt']);

        $result = $host->exposeConvertRequestToArray($request);

        self::assertSame(['file' => ['name' => 'upload.txt']], $result);
    }

    public function testHandleApiCallThrowsOnNullRequest(): void
    {
        $stack = new RequestStack();

        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')->willReturnCallback(fn(string $id) => match ($id) {
            'request_stack' => $stack,
        });

        $host = new class($container) {
            use ApiFormTrait;

            protected \Psr\Container\ContainerInterface $container;

            public function __construct(ContainerInterface $container)
            {
                $this->container = $container;
            }

            public function exposeHandleApiCall(FormInterface|string $form): mixed
            {
                return $this->handleApiCall($form);
            }
        };

        $form = $this->createStub(FormInterface::class);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot handle API call without an active request.');

        $host->exposeHandleApiCall($form);
    }
}
