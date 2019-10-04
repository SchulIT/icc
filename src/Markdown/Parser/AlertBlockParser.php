<?php

namespace App\Markdown\Parser;

use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use App\Markdown\Element\AlertBlock;

class AlertBlockParser implements BlockParserInterface {

    /**
     * @inheritDoc
     */
    public function parse(ContextInterface $context, Cursor $cursor): bool {
        $previousState = $cursor->saveState();

        $alert = $cursor->match('/^!{3}([a-z]+)/');
        if($alert === null) {
            $cursor->restoreState($previousState);
            return false;
        }

        $type = substr($alert, 3);

        $context->addBlock(new AlertBlock($type));

        return true;
    }
}