<?php

declare(strict_types=1);

namespace Tests\Unit\View;

use ChamberOrchestra\FormBundle\View\SuccessView;
use ChamberOrchestra\ViewBundle\View\ResponseView;
use PHPUnit\Framework\TestCase;

final class SuccessViewTest extends TestCase
{
    public function testExtendsResponseView(): void
    {
        $view = new SuccessView();

        self::assertInstanceOf(ResponseView::class, $view);
    }
}
