<?php

namespace App\Markdown\Renderer\Inline;

use App\Markdown\Node\Inline\Icon;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use Stringable;

class IconRenderer implements NodeRendererInterface {

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): ?Stringable {
        Icon::assertInstanceOf($node);
        assert($node instanceof Icon);

        return new HtmlElement(
            'i',
            [
                'class' => $node->getIcon()
            ]
        );
    }
}