<?php

namespace App\Grouping;

interface SortableGroupInterface {
    public function &getItems(): array;
}