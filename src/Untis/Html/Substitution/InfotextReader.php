<?php

namespace App\Untis\Html\Substitution;

class InfotextReader implements InfotextReaderInterface {

    public function canHandle(?string $identifier): bool {
        return $identifier === null;
    }

    public function handle(SubstitutionResult $result, string $content): void {
        $result->addInfotext(new Infotext($content));
    }
}