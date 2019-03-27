<?php

namespace App\Markdown\Processor;

use League\CommonMark\Block\Element\Document;
use League\CommonMark\DocumentProcessorInterface;
use League\CommonMark\Inline\Element\HtmlInline;
use League\CommonMark\Inline\Element\Link;
use App\Repository\DocumentRepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkProcessor implements DocumentProcessorInterface {

    private $documentRepository;
    private $urlGenerator;

    public function __construct(DocumentRepositoryInterface $documentRepository, UrlGeneratorInterface $urlGenerator) {
        $this->documentRepository = $documentRepository;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritDoc
     */
    public function processDocument(Document $document) {
        $walker = $document->walker();

        while($event = $walker->next()) {
            $node = $event->getNode();

            if(!$node instanceof Link || !$event->isEntering()) {
                continue;
            }

            $node->data['attributes']['class'] = 'link';

            $url = $node->getUrl();
            if(substr($url, 0, 7) === 'mailto:') {
                $node->data['attributes']['class'] = 'mail';
            } else if(substr($url, 0, 1)  === '!') {
                $id = substr($url, 1);
                $document = $this->documentRepository->findOneById($id);

                if($document !== null) {
                    $url = $this->urlGenerator->generate('show_document', [
                        'id' => $document->getId(),
                        'alias' => $document->getAlias()
                    ]);
                    $node->setUrl($url);
                } else {
                    $node->setUrl('#');
                    $node->appendChild(new HtmlInline(' (Dokument nicht mehr verfügbar)'));
                }
            }
        }
    }
}