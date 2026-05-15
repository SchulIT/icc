<?php

namespace App\Framework\View\Filter;

interface FilterViewInterface {
    public function isEnabled(): bool;
}