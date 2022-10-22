<?php

namespace App\Markdown\Element;

use League\CommonMark\Inline\Element\AbstractInline;

class Icon extends AbstractInline {
    public function __construct(private string $icon)
    {
    }

    public function setIcon(string $icon): void {
        $this->icon = $icon;
    }

    public function getIcon(): string {
        return $this->icon;
    }
}