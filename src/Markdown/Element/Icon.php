<?php

namespace App\Markdown\Element;

use League\CommonMark\Inline\Element\AbstractInline;

class Icon extends AbstractInline {
    private $icon;

    public function __construct(string $icon) {
        $this->icon = $icon;
    }

    public function setIcon(string $icon): void {
        $this->icon = $icon;
    }

    public function getIcon(): string {
        return $this->icon;
    }
}