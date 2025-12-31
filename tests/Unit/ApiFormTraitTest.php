<?php

declare(strict_types=1);

namespace Tests\Unit;

use ChamberOrchestra\FormBundle\ApiFormTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
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
}
