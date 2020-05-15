<?php
/**
 * Foo
 */

namespace Raj\StorageBundle\Utils;


class Sorter {

    public function sortByAscendingId($a, $b) {
        if ($a->id == $b->id) {
            return 0;
        }
        return ($a->id < $b->id) ? -1 : 1;
    }
}