<?php

namespace App\Markdown\Renderer\Inline;

use App\Markdown\Node\Inline\Anchor;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use Stringable;

class AnchorRenderer implements NodeRendererInterface {

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): ?Stringable {
        Anchor::assertInstanceOf($node);
        assert($node instanceof Anchor);

        return new HtmlElement(
            'a', [
                'id' => $node->getId(),
                'href' => '#' . $node->getId(),
            ],
            $childRenderer->renderNodes($node->children())
        );
    }
}