<?php

namespace App\Markdown\Node\Inline;

use League\CommonMark\Node\Inline\AbstractInline;

class Icon extends AbstractInline {
    public function __construct(private readonly string $icon) {

    }

    public function getIcon(): string {
        return $this->icon;
    }
}