<?php

namespace App\View\Filter;

interface FilterViewInterface {
    public function isEnabled(): bool;
}