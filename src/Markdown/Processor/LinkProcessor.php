<?php

namespace App\Markdown\Processor;

use App\Markdown\Element\Icon;
use App\Repository\DocumentRepositoryInterface;
use App\Repository\WikiArticleRepositoryInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Inline\Element\HtmlInline;
use League\CommonMark\Inline\Element\Link;
use Ramsey\Uuid\Uuid;
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
            } else if(substr($url, 0, 9)  === 'document:') {
                $uuid = substr($url, 9);

                if(Uuid::isValid($uuid)) {
                    $document = $this->documentRepository->findOneByUuid($uuid);

                    if ($document !== null) {
                        $url = $this->urlGenerator->generate('show_document', [
                            'uuid' => $document->getUuid()
                        ]);
                        $node->setUrl($url);
                    } else {
                        $this->appendBroken($node);
                    }
                } else {
                    $this->appendBroken($node);
                }
            } else if(substr($url, 0, 5) === 'wiki:') {
                $uuid = substr($url, 5);

                if(Uuid::isValid($uuid)) {
                    $article = $this->wikiArticleRepository->findOneByUuid($uuid);

                    if ($article !== null) {
                        $url = $this->urlGenerator->generate('show_wiki_article', [
                            'uuid' => $article->getUuid()
                        ]);
                        $node->setUrl($url);
                    } else {
                        $this->appendBroken($node);
                    }
                } else {
                    $this->appendBroken($node);
                }
            }
        }
    }

    private function appendBroken(Link $node) {
        $node->setUrl('');
        $node->prependChild(new HtmlInline(' '));

        $icon = new Icon('fas fa-unlink');
        $icon->data['attributes']['title'] = $this->translator->trans('markdown.link_broken');

        $node->prependChild($icon);
    }
}