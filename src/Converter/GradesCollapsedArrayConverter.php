<?php

namespace App\Converter;

use App\Entity\Grade;
use App\Utils\ArrayUtils;

/**
 * Converts an iterable of Grade into a collapsed array of grade names.
 */
class GradesCollapsedArrayConverter {

    private const Threshold = 3;

    /**
     * @param Grade[] $grades
     * @return string[]
     */
    public function convert(iterable $grades): array {
        $grades = ArrayUtils::iterableToArray($grades);

        if(count($grades) < self::Threshold) {
            return array_map(function(Grade $grade) {
                return $grade->getName();
            }, $grades);
        }

        $collapsed = [];

        foreach($grades as $grade) {
            $added = false;

            if($grade->allowCollapse() === false) {
                $collapsed[] = $grade->getName();
                continue;
            }

            for($i = 0; $i < count($collapsed) && $added === false; $i++) {
                $existingGrade = $collapsed[$i];
                $suffix = $this->getSuffixIfSamePrefix($existingGrade, $grade->getName());

                if($suffix !== null) {
                    $collapsed[$i] .= $suffix;
                    $added = true;
                }
            }

            if($added === false) {
                $collapsed[] = $grade->getName();
            }
        }

        return $collapsed;
    }

    private function getSuffixIfSamePrefix(string $existingGrade, string $grade): ?string {
        $existingGrade = ltrim($existingGrade, '0');
        $grade = ltrim($grade, '0');

        $length = min(mb_strlen($existingGrade), mb_strlen($grade));
        $pos = 0;

        while($pos < $length && mb_substr($existingGrade, $pos, 1) === mb_substr($grade, $pos, 1)) {
            $pos++;
        }

        if($pos > 0) {
            return mb_substr($grade, $pos);
        }

        return null;
    }
}