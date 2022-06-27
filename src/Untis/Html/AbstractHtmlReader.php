<?php

namespace App\Untis\Html;

use DOMDocument;
use DOMXPath;

abstract class AbstractHtmlReader {
    protected function fixHtml(string $html): string {
        $html = str_replace('<p>', '', $html);
        return str_replace('&nbsp;', ' ', $html);
    }


    protected function getXPath(string $html): DOMXPath {
        libxml_use_internal_errors(true); libxml_clear_errors();
        $html = $this->fixHtml($html);

        $document = new DOMDocument();
        $document->loadHTML($html);

        return new DOMXPath($document);
    }

}