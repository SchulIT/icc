<?php

namespace App\Markdown\Parser\Block;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

class AlertStartParser implements BlockStartParserInterface {

    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart {
        if($cursor->isIndented() || $cursor->getNextNonSpaceCharacter() !== '!') {
            return BlockStart::none();
        }

        $match = $cursor->match('/^!{3}([a-z]+)/');
        if($match === null) {
            return BlockStart::none();
        }

        $type = substr($match, 3);

        return BlockStart::of(new AlertParser($type))->at($cursor);
    }
}