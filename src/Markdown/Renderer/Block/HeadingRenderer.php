<?php

namespace App\Markdown\Renderer\Block;

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class HeadingRenderer implements NodeRendererInterface{

    public function render(Node $node, ChildNodeRendererInterface $childRenderer) {
        Heading::assertInstanceOf($node);
        assert($node instanceof Heading);

        $level = $node->getLevel();
        $level = min($level + 4, 6);
        $tag = 'h' . $level;

        return new HtmlElement(
            $tag,
            $node->data->get('attributes'),
            $childRenderer->renderNodes($node->children())
        );
    }
}