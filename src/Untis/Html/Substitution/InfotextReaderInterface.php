<?php

namespace App\Untis\Html\Substitution;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.untis.import.infotext_reader')]
interface InfotextReaderInterface {
    public function canHandle(?string $identifier): bool;

    public function handle(SubstitutionResult $result, string $content): void;
}