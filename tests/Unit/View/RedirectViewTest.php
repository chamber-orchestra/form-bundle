<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\View;

use ChamberOrchestra\FormBundle\View\RedirectView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class RedirectViewTest extends TestCase
{
    public function testStatusAndLocation(): void
    {
        $view = new RedirectView('/next', Response::HTTP_FOUND);

        self::assertSame(Response::HTTP_FOUND, $view->status);
        self::assertSame('/next', $view->location);
    }
}
