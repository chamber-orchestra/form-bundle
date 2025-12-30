<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\View;

use ChamberOrchestra\ViewBundle\View\ViewInterface;
use Symfony\Component\HttpFoundation\Response;

class RedirectView implements ViewInterface
{
    public int $status;
    public string $location;

    public function getStatus(): int
    {
        return $this->status;
    }

    public function __construct(string $location, int $status = Response::HTTP_MOVED_PERMANENTLY)
    {
        $this->location = $location;
        $this->status = $status;
    }
}