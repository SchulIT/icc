<?php

namespace App\Markdown\Parser\Block;

use App\Markdown\Node\Block\Alert;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Util\RegexHelper;

class AlertParser extends AbstractBlockContinueParser {

    private readonly Alert $alert;

    public function __construct(private readonly string $type) {
        $this->alert = new Alert($this->type);
    }

    public function getBlock(): AbstractBlock {
        return $this->alert;
    }

    public function isContainer(): bool {
        return true;
    }

    public function canContain(AbstractBlock $childBlock): bool {
        return true;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue {
        if(!$cursor->isIndented() && $cursor->getNextNonSpaceCharacter() === '!') {
            $match = RegexHelper::matchFirst('/^!{3}/', $cursor->getLine(), $cursor->getNextNonSpacePosition());

            if($match !== null) {
                return BlockContinue::finished();
            }
        }

        return BlockContinue::at($cursor);
    }
}