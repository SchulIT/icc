<?php

namespace App\Twig;

use App\Entity\TuitionGradeCatalog;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class GradebookHelper extends AbstractExtension {
    public function getFilters(): array {
        return [
            new TwigFilter('gradeCatalogColorMap', [ $this, 'colorMap'])
        ];
    }

    public function colorMap(TuitionGradeCatalog $catalog): array {
        $colorMap = [ ];

        foreach($catalog->getGrades() as $grade) {
            $colorMap[$grade->getValue()] = $grade->getColor();
        }

        return $colorMap;
    }
}