<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\FormBundle\View;

use ChamberOrchestra\ViewBundle\View\View;

class ViolationView extends View
{
    public function __construct(
        public string $id,
        public string $title,
        public array $parameters,
        public string $propertyPath,
        public string|null $type = null,
    ) {
    }
}