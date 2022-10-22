<?php

namespace App\Markdown\Element;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Cursor;

class AlertBlock extends AbstractBlock {

    public function __construct(private $type)
    {
    }

    public function getType() {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function canContain(AbstractBlock $block): bool {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function acceptsLines(): bool {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isCode(): bool {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function matchesNextLine(Cursor $cursor): bool {
        $line = $cursor->getLine();

        if($line === '!!!') {
            $cursor->advanceBy(3);

            return false;
        }

        return true;
    }
}