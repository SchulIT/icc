<?php

namespace App\Untis\Html;

interface InfotextReaderInterface {
    public function canHandle(?string $identifier): bool;

    public function handle(HtmlSubstitutionResult $result, string $content): void;
}