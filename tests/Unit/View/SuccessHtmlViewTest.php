<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
