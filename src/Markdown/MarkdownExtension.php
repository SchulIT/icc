<?php

namespace App\Markdown;

use App\Markdown\Node\Block\Alert;
use App\Markdown\Node\Inline\Anchor;
use App\Markdown\Node\Inline\Icon;
use App\Markdown\Parser\Block\AlertStartParser;
use App\Markdown\Processor\HeadingAnchorProcessor;
use App\Markdown\Processor\ImageProcessor;
use App\Markdown\Processor\LinkProcessor;
use App\Markdown\Renderer\Block\AlertRenderer;
use App\Markdown\Renderer\Block\HeadingRenderer;
use App\Markdown\Renderer\Inline\AnchorRenderer;
use App\Markdown\Renderer\Inline\IconRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\ExtensionInterface;

class MarkdownExtension implements ExtensionInterface {

    public function __construct(private readonly AlertStartParser $alertParser,
                                private readonly AlertRenderer $alertRenderer,
                                private readonly HeadingAnchorProcessor $headingProcessor,
                                private readonly LinkProcessor $linkProcessor,
                                private readonly ImageProcessor $imageProcessor,
                                private readonly HeadingRenderer $headingRenderer,
                                private readonly AnchorRenderer $anchorRenderer,
                                private readonly IconRenderer $iconRenderer)
    {
    }

    public function register(EnvironmentBuilderInterface $environment): void {
        $environment
            ->addBlockStartParser($this->alertParser, 260)
            ->addRenderer(Alert::class, $this->alertRenderer, 300)
            ->addRenderer(Heading::class, $this->headingRenderer, 100)
            ->addRenderer(Icon::class, $this->iconRenderer)
            ->addRenderer(Anchor::class, $this->anchorRenderer)
            ->addEventListener(DocumentParsedEvent::class, [ $this->headingProcessor, 'onDocumentParsed' ] , 0)
            ->addEventListener(DocumentParsedEvent::class, [ $this->linkProcessor, 'onDocumentParsed' ] , 0)
            ->addEventListener(DocumentParsedEvent::class, [ $this->imageProcessor, 'onDocumentParsed'], 0);
    }
}