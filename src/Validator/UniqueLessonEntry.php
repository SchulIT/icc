<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class UniqueLessonEntry extends Constraint {

    public string $message = 'There is already an existing lesson entry for this lesson.';

    /**
     * {@inheritdoc}
     */
    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }
}