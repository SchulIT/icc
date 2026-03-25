<?php

namespace App\Autocomplete;

readonly class Response {
    public function __construct(
        public int $page,
        public int $pages,
        public int $count,
        public array $items
    ) {

    }
}