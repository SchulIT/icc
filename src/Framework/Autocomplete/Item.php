<?php

namespace App\Framework\Autocomplete;

readonly class Item {
    public function __construct(
        public string $id,
        public string $label,
        public string|null $sublabel = null,
        public string|null $extra = null
    ) {

    }
}