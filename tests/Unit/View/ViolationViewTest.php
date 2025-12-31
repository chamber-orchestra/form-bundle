<?php

declare(strict_types=1);

namespace Tests\Unit\View;

use ChamberOrchestra\FormBundle\View\ViolationView;
use PHPUnit\Framework\TestCase;

final class ViolationViewTest extends TestCase
{
    public function testStoresViolationData(): void
    {
        $view = new ViolationView('id', 'Title', ['key' => 'value'], 'path', 'type');

        self::assertSame('id', $view->id);
        self::assertSame('Title', $view->title);
        self::assertSame(['key' => 'value'], $view->parameters);
        self::assertSame('path', $view->propertyPath);
        self::assertSame('type', $view->type);
    }
}
