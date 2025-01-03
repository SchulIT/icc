<?php

namespace App\Markdown\Node\Block;

use League\CommonMark\Node\Block\AbstractBlock;

class Alert extends AbstractBlock {
    public function __construct(private readonly string $type) {

    }

    public function getType(): string {
        return $this->type;
    }
}