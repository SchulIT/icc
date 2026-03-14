<?php

namespace App\Tools\WestermannZvs\Check;

readonly class Action {
    public function __construct(
        public string|null $messageKey,
        public array $messageParameters = [ ]
    ) { }
}