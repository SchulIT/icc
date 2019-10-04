<?php

namespace App\Markdown\Renderer;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\Ext\Table\Table;
use League\CommonMark\HtmlElement;
use League\CommonMark\Util\Xml;

class TableRenderer implements BlockRendererInterface {

    /**
     * @inheritDoc
     */
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, bool $inTightList = false) {
        if (!$block instanceof Table) {
            throw new \InvalidArgumentException('Incompatible block type: '.get_class($block));
        }

        $attrs = [];
        foreach ($block->getData('attributes', []) as $key => $value) {
            $attrs[$key] = Xml::escape($value);
        }

        $attrs['class'] = 'table table-striped table-hover';

        $separator = $htmlRenderer->getOption('inner_separator', "\n");

        $table = new HtmlElement('table', $attrs, $separator.$htmlRenderer->renderBlocks($block->children()).$separator);
        return new HtmlElement('div', ['class' => 'table-responsive'], $separator.(string)$table);
    }
}