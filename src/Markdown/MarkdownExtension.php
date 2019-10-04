<?php

namespace App\Markdown;

use App\Markdown\Element\AlertBlock;
use App\Markdown\Element\AnchorLink;
use App\Markdown\Parser\AlertBlockParser;
use App\Markdown\Processor\HeadingAnchorProcessor;
use App\Markdown\Processor\LinkProcessor;
use App\Markdown\Renderer\AlertBlockRenderer;
use App\Markdown\Renderer\HeadingRenderer;
use App\Markdown\Renderer\TableRenderer;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Ext\Table\Table;
use League\CommonMark\Ext\Table\TableCaption;
use League\CommonMark\Ext\Table\TableCaptionRenderer;
use League\CommonMark\Ext\Table\TableCell;
use League\CommonMark\Ext\Table\TableCellRenderer;
use League\CommonMark\Ext\Table\TableParser;
use League\CommonMark\Ext\Table\TableRow;
use League\CommonMark\Ext\Table\TableRowRenderer;
use League\CommonMark\Ext\Table\TableSection;
use League\CommonMark\Ext\Table\TableSectionRenderer;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Inline\Renderer\LinkRenderer;

class MarkdownExtension implements ExtensionInterface {

    private $alertBlockParser;

    private $headingProcessor;
    private $linkProcessor;

    private $alertBlockRenderer;
    private $headingRenderer;
    private $tableRenderer;

    public function __construct(AlertBlockParser $alertBlockParser, HeadingAnchorProcessor $headingProcessor, LinkProcessor $linkProcessor,
                                AlertBlockRenderer $alertBlockRenderer, HeadingRenderer $headingRenderer, TableRenderer $tableRenderer) {
        $this->alertBlockParser = $alertBlockParser;
        $this->headingProcessor = $headingProcessor;
        $this->linkProcessor = $linkProcessor;
        $this->alertBlockRenderer = $alertBlockRenderer;
        $this->headingRenderer = $headingRenderer;
        $this->tableRenderer = $tableRenderer;
    }

    public function register(ConfigurableEnvironmentInterface $environment) {
        $environment
            ->addBlockParser(new TableParser())
            ->addBlockRenderer(Heading::class, $this->headingRenderer, 100)
            ->addBlockRenderer(Table::class, $this->tableRenderer)
            ->addBlockRenderer(TableCaption::class, new TableCaptionRenderer())
            ->addBlockRenderer(TableSection::class, new TableSectionRenderer())
            ->addBlockRenderer(TableRow::class, new TableRowRenderer())
            ->addBlockRenderer(TableCell::class, new TableCellRenderer())
            ->addBlockParser($this->alertBlockParser)
            ->addInlineRenderer(AnchorLink::class, new LinkRenderer())
            ->addBlockRenderer(AlertBlock::class, $this->alertBlockRenderer)
            ->addEventListener(DocumentParsedEvent::class, [ $this->headingProcessor, 'onDocumentParsed' ] , 0)
            ->addEventListener(DocumentParsedEvent::class, [ $this->linkProcessor, 'onDocumentParsed' ] , 0);
    }
}