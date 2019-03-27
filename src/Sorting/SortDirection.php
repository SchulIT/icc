<?php

namespace App\Sorting;

use MyCLabs\Enum\Enum;

/**
 * @method static SortDirection Ascending()
 * @method static SortDirection Descending()
 */
class SortDirection extends Enum {
    private const Ascending = 'asc';
    private const Descending = 'desc';
}