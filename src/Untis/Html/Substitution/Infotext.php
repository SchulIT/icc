<?php

namespace App\Untis\Html\Substitution;

class Infotext {
    public function __construct(private string $content)
    {
    }

    public function getContent(): string {
        return $this->content;
    }
}