<?php

namespace App\Markdown\Processor;

use App\Repository\DocumentRepositoryInterface;
use App\Repository\WikiArticleRepositoryInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Inline\Element\HtmlInline;
use League\CommonMark\Inline\Element\Link;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LinkProcessor {

    private $documentRepository;
    private $wikiArticleRepository;
    private $urlGenerator;
    private $translator;

    public function __construct(DocumentRepositoryInterface $documentRepository, WikiArticleRepositoryInterface $wikiArticleRepository,
                                UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator) {
        $this->documentRepository = $documentRepository;
        $this->wikiArticleRepository = $wikiArticleRepository;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function onDocumentParsed(DocumentParsedEvent $event) {
        $document = $event->getDocument();
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
            } else if(substr($url, 0, 8)  === 'document:') {
                $id = substr($url, 8);
                $document = $this->documentRepository->findOneById($id);

                if($document !== null) {
                    $url = $this->urlGenerator->generate('show_document', [
                        'id' => $document->getId(),
                        'slug' => $document->getSlug()
                    ]);
                    $node->setUrl($url);
                } else {
                    $node->setUrl('#');
                    $node->appendChild(new HtmlInline('broken'));
                }
            } else if(substr($url, 0, 5) === 'wiki:') {
                $id = intval(substr($url, 5));
                $article = $this->wikiArticleRepository->findOneById($id);

                if($article !== null) {
                    $url = $this->urlGenerator->generate('show_wiki_article', [
                        'id' => $article->getId(),
                        'slug' => $article->getSlug()
                    ]);
                    $node->setUrl($url);
                } else {
                    $node->setUrl('#');
                    $node->appendChild(new HtmlInline('broken'));
                }
            }
        }
    }
}