<?php

declare(strict_types=1);

namespace Tests\Unit\View;

use ChamberOrchestra\FormBundle\View\SuccessHtmlView;
use ChamberOrchestra\ViewBundle\View\DataView;
use PHPUnit\Framework\TestCase;

final class SuccessHtmlViewTest extends TestCase
{
    public function testExtendsDataView(): void
    {
        $view = new SuccessHtmlView(['html' => '<p>ok</p>']);

        self::assertInstanceOf(DataView::class, $view);
    }
}
