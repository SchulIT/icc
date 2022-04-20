<?php

namespace App\Untis\Html;

class HtmlInfotext {
    private string $content;

    public function __construct(string $content) {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent(): string {
        return $this->content;
    }
}