<?php

namespace App\Markdown\Processor;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Inline\Element\Image;
use League\CommonMark\Inline\Element\Newline;

class ImageProcessor {
    public function onDocumentParsed(DocumentParsedEvent $event): void {
        $document = $event->getDocument();
        $walker = $document->walker();

        while($event = $walker->next()) {
            $node = $event->getNode();

            if (!$node instanceof Image || !$event->isEntering()) {
                continue;
            }

            if(($node->next() === null || $node->next() instanceof Image || $node->next() instanceof Newline) && ($node->previous() === null || $node->previous() instanceof Image || $node->previous() instanceof Newline)) {
                $node->data['attributes']['class'] = 'img-fluid mx-auto d-block';
            }
        }
    }
}