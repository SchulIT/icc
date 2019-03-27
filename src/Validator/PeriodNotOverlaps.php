<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class PeriodNotOverlaps extends Constraint {
    public $message = 'The period overlaps with period "{{ period }}".';

    /**
     * {@inheritdoc}
     */
    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy() {
        return 'validator.period_not_overlaps';
    }
}