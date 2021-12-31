<?php

namespace App\Twig;

use App\Markdown\Markdown;
use App\Markdown\TableOfContentsHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MarkdownExtension extends AbstractExtension {

    private Markdown $markdown;
    private TableOfContentsHelper $tocHelper;

    public function __construct(Markdown $markdown, TableOfContentsHelper $tocHelper) {
        $this->markdown = $markdown;
        $this->tocHelper = $tocHelper;
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('toc', [ $this, 'toc' ])
        ];
    }

    public function getFilters(): array {
        return [
            new TwigFilter('markdown', [$this, 'markdown'], ['is_safe' => ['html']]),
        ];
    }

    public function markdown(string $string): string {
        return $this->markdown->convertToHtml($string);
    }

    public function toc(string $markdown): array {
        return $this->tocHelper->getTableOfContents($markdown);
    }
}