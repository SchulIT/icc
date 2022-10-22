<?php

namespace App\Markdown\Processor;

use App\Markdown\Element\AnchorLink;
use App\Markdown\Element\Icon;
use EasySlugger\SluggerInterface;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Inline\Element\HtmlInline;

class HeadingAnchorProcessor {

    public function __construct(private SluggerInterface $slugger)
    {
    }

    /**
     * @inheritDoc
     */
    public function onDocumentParsed(DocumentParsedEvent $event) {
        $document = $event->getDocument();
        $walker = $document->walker();

        while($event = $walker->next()) {
            $node = $event->getNode();

            if(!($node instanceof Heading) || !$event->isEntering()) {
                continue;
            }

            $heading = $node->getStringContent();
            $slug = $this->slugger->slugify($heading);

            $linkChild = new AnchorLink($slug);
            $linkChild->appendChild(new Icon('far fa-bookmark'));

            $node->prependChild(new HtmlInline(' '));
            $node->prependChild($linkChild);
        }
    }
}