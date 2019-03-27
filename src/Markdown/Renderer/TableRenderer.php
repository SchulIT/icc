<?php

namespace App\Markdown\Renderer;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;

class TableRenderer extends \Webuni\CommonMark\TableExtension\TableRenderer {

    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false) {
        $htmlElement = parent::render($block, $htmlRenderer, $inTightList);
        $htmlElement->setAttribute('class', 'table table-striped table-hover');

        $parentDiv = new HtmlElement('div', ['class' => 'table-responsive'], $htmlElement);

        return $parentDiv;
    }
}