<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use ChamberOrchestra\FormBundle\Utils\CollectionUtils;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

final class CollectionUtilsTest extends TestCase
{
    public function testSyncAddsAndRemovesItems(): void
    {
        $source = new ArrayCollection([1, 2, 3]);
        $target = [2, 3, 4];

        CollectionUtils::sync($source, $target);

        self::assertSame([2, 3, 4], array_values($source->toArray()));
    }
}
