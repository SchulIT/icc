<?php

namespace App\Markdown\Node\Inline;

use League\CommonMark\Node\Inline\AbstractInline;

class Anchor extends AbstractInline {
    public function __construct(private readonly string $id) {
        parent::__construct();
    }

    public function getId(): string {
        return $this->id;
    }
}