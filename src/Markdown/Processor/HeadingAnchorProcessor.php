<?php

namespace App\Markdown\Processor;

use App\Markdown\Element\AnchorLink;
use App\Markdown\Node\Inline\Anchor;
use App\Markdown\Node\Inline\Icon;
use EasySlugger\SluggerInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Inline\Text;

class HeadingAnchorProcessor {

    public function __construct(private SluggerInterface $slugger)
    {
    }

    /**
     * @inheritDoc
     */
    public function onDocumentParsed(DocumentParsedEvent $event): void {
        $document = $event->getDocument();
        $walker = $document->walker();

        while($event = $walker->next()) {
            $node = $event->getNode();

            if(!($node instanceof Heading) || !$event->isEntering()) {
                continue;
            }


            $text = $node->firstChild();

            if(!$text instanceof Text) {
                continue;
            }

            $heading = $text->getLiteral();
            $slug = $this->slugger->slugify($heading);

            $linkChild = new Anchor($slug);
            $linkChild->appendChild(new Icon('far fa-bookmark'));

            $node->prependChild(new HtmlInline(' '));
            $node->prependChild($linkChild);
        }
    }
}