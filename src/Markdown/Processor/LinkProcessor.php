<?php

namespace App\Markdown\Processor;

use App\Markdown\Node\Inline\Icon;
use App\Repository\DocumentRepositoryInterface;
use App\Repository\WikiArticleRepositoryInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LinkProcessor {

    public function __construct(private readonly DocumentRepositoryInterface $documentRepository,
                                private readonly WikiArticleRepositoryInterface $wikiArticleRepository,
                                private readonly UrlGeneratorInterface  $urlGenerator,
                                private readonly TranslatorInterface $translator)
    {
    }

    public function onDocumentParsed(DocumentParsedEvent $event): void {
        $document = $event->getDocument();
        $walker = $document->walker();

        while($event = $walker->next()) {
            $node = $event->getNode();

            if(!$node instanceof Link || !$event->isEntering()) {
                continue;
            }

            $url = $node->getUrl();

            $node->data->set('attributes/class', 'btn btn-outline-primary btn-sm');

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
                $node->data->set('attributes/target', '_blank');
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
        $icon->data->set('attributes/title',$this->translator->trans('markdown.link_broken'));
    }
}