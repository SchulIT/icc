<?php

namespace App\Markdown\Processor;

use App\Markdown\Element\AnchorLink;
use EasySlugger\SluggerInterface;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\Event\DocumentParsedEvent;

class HeadingAnchorProcessor {

    /**
     * @var SluggerInterface
     */
    private $slugger;

    public function __construct(SluggerInterface $slugger) {
        $this->slugger = $slugger;
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
            $node->prependChild($linkChild);
        }
    }
}