<?php

namespace App\Converter;

use App\Entity\Grade;
use App\Utils\ArrayUtils;

/**
 * Converts an iterable of Grade into a string.
 */
class GradesStringConverter {

    private GradesCollapsedArrayConverter $collapsedArrayConverter;

    public function __construct(GradesCollapsedArrayConverter $collapsedArrayConverter) {
        $this->collapsedArrayConverter = $collapsedArrayConverter;
    }

    public function convert(iterable $grades, bool $collapse = true): string {
        $grades = ArrayUtils::iterableToArray($grades);

        if($collapse === false) {
            return implode(
                ', ',
                array_map(function(Grade $grade) {
                    return $grade->getName();
                }, $grades)
            );
        }

        return implode(', ', $this->collapsedArrayConverter->convert($grades));
    }
}