<?php

namespace App\Markdown\Renderer\Block;

use App\Markdown\Node\Block\Alert;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use Stringable;

class AlertRenderer implements NodeRendererInterface {

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): ?Stringable {
        Alert::assertInstanceOf($node);
        assert($node instanceof Alert);

        return new HtmlElement(
            'div',
            [
                'class' => ['alert alert-' . $node->getType() ],
            ],
            $childRenderer->renderNodes($node->children())
        );
    }
}