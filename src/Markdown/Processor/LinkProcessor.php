<?php

namespace App\Markdown\Processor;

use App\Markdown\Element\AnchorLink;
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

    public function __construct(private DocumentRepositoryInterface $documentRepository, private WikiArticleRepositoryInterface $wikiArticleRepository, private UrlGeneratorInterface $urlGenerator, private TranslatorInterface $translator)
    {
    }

    public function onDocumentParsed(DocumentParsedEvent $event): void {
        $document = $event->getDocument();
        $walker = $document->walker();

        while($event = $walker->next()) {
            $node = $event->getNode();

            if(!$node instanceof Link || $node instanceof AnchorLink || !$event->isEntering()) {
                continue;
            }

            $url = $node->getUrl();

            $node->data['attributes']['class'] = 'btn btn-outline-primary btn-sm';

            if(str_starts_with($url, 'mailto:')) {
                $this->prependIcon($node, 'far fa-envelope');
            } else if(str_starts_with($url, 'document:')) {
                $uuid = substr($url, 9);

                if(Uuid::isValid($uuid)) {
                    $document = $this->documentRepository->findOneByUuid($uuid);

                    if ($document !== null) {
                        $url = $this->urlGenerator->generate('show_document', [
                            'uuid' => $document->getUuid()
                        ], UrlGeneratorInterface::ABSOLUTE_URL);
                        $node->setUrl($url);
                        $this->prependIcon($node, 'fas fa-arrow-right');
                    } else {
                        $this->appendBroken($node);
                    }
                } else {
                    $this->appendBroken($node);
                }
            } else if(str_starts_with($url, 'wiki:')) {
                $uuid = substr($url, 5);

                if(Uuid::isValid($uuid)) {
                    $article = $this->wikiArticleRepository->findOneByUuid($uuid);

                    if ($article !== null) {
                        $url = $this->urlGenerator->generate('show_wiki_article', [
                            'uuid' => $article->getUuid()
                        ], UrlGeneratorInterface::ABSOLUTE_URL);
                        $node->setUrl($url);
                        $this->prependIcon($node, 'fas fa-arrow-right');
                    } else {
                        $this->appendBroken($node);
                    }
                } else {
                    $this->appendBroken($node);
                }
            } else {
                $this->prependIcon($node, 'fas fa-external-link-alt');
            }
        }
    }

    private function prependIcon(Link $node, string $class): Icon {
        $icon = new Icon($class);
        $node->prependChild(new HtmlInline(' '));
        $node->prependChild($icon);

        return $icon;
    }

    private function appendBroken(Link $node): void {
        $node->setUrl('');
        $icon = $this->prependIcon($node, 'fas fa-unlink');
        $icon->data['attributes']['title'] = $this->translator->trans('markdown.link_broken');
    }
}