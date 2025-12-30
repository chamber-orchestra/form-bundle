<?php

namespace ChamberOrchestra\FormBundle\Utils;

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CollectionUtils
{
    public static function sync(Collection $source, iterable $target): void
    {
        $clone = clone $source;
        $target = new ArrayCollection(\is_array($target) ? $target : \iterator_to_array($target));

        //add new
        foreach ($target as $item) {
            if (!$clone->contains($item)) {
                $source->add($item);
            }
        }

        //remove old
        foreach ($clone as $item) {
            if (!$target->contains($item)) {
                $source->removeElement($item);
            }
        }
    }
}