<?php

namespace App\Untis\Html;

class HtmlInfotextReader implements InfotextReaderInterface {

    public function canHandle(?string $identifier): bool {
        return $identifier === null;
    }

    public function handle(HtmlSubstitutionResult $result, string $content): void {
        $result->addInfotext(new HtmlInfotext($content));
    }
}