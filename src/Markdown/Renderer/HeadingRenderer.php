<?php

namespace App\Markdown\Renderer;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Util\Xml;

class HeadingRenderer implements BlockRendererInterface {

    /**
     * @inheritDoc
     */
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false): string|HtmlElement|null {
        if(!$block instanceof Heading) {
            throw new \InvalidArgumentException(sprintf('$block must be of type "%s" ("%s" given)', Heading::class, get_class($block)));
        }

        $level = $block->getLevel();
        $level = min($level + 4, 6);

        $tag = 'h' . $level;

        $attrs = [];
        foreach ($block->getData('attributes', []) as $key => $value) {
            $attrs[$key] = Xml::escape($value);
        }

        return new HtmlElement($tag, $attrs, $htmlRenderer->renderInlines($block->children()));
    }
}