<?php

namespace App\Untis\Html\Substitution;

interface InfotextReaderInterface {
    public function canHandle(?string $identifier): bool;

    public function handle(SubstitutionResult $result, string $content): void;
}