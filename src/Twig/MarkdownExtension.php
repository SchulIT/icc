<?php

namespace App\Twig;

use App\Markdown\Markdown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension {

    private $markdown;

    public function __construct(Markdown $markdown) {
        $this->markdown = $markdown;
    }

    public function getFilters() {
        return [
            new TwigFilter('markdown', [ $this, 'markdown' ], [ 'is_safe' => ['html'] ])
        ];
    }

    public function markdown(string $string): string {
        return $this->markdown->convertToHtml($string);
    }
}