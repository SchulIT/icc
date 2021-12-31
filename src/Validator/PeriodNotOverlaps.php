<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class PeriodNotOverlaps extends Constraint {
    public string $message = 'Period overlaps with period "{{ key }}".';

    /**
     * {@inheritdoc}
     */
    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }
}