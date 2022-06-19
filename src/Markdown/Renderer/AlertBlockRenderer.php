<?php

namespace App\Markdown\Renderer;

use App\Markdown\Element\AlertBlock;
use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;

class AlertBlockRenderer implements BlockRendererInterface {

    /**
     * @inheritDoc
     */
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false): string|HtmlElement|null {
        if(!$block instanceof AlertBlock) {
            throw new \InvalidArgumentException(sprintf('$block must be of type "%s" ("%s" given)', AlertBlock::class, get_class($block)));
        }

        return new HtmlElement('div', [
            'class' => 'alert alert-' . $block->getType(),
        ], $htmlRenderer->renderBlocks($block->children()));
    }
}