<?php

namespace App\Markdown\Processor;

use EasySlugger\SluggerInterface;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\DocumentProcessorInterface;
use App\Markdown\Element\AnchorLink;

class HeadingAnchorProcessor implements DocumentProcessorInterface {

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
    public function processDocument(Document $document) {
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