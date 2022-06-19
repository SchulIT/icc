<?php

namespace App\Markdown\Renderer;

use App\Markdown\Element\Icon;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

class IconRenderer implements InlineRendererInterface {

    /**
     * @inheritDoc
     */
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer): string|HtmlElement|null {
        if(!$inline instanceof Icon) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . \get_class($inline));
        }

        $attrs = $inline->getData('attributes', []);
        $attrs['class'] = $inline->getIcon();

        return new HtmlElement('i', $attrs);
    }
}