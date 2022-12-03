<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
class UniqueLessonEntry extends Constraint {

    public string $message = 'There is already an existing lesson entry for this lesson.';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): array|string {
        return self::CLASS_CONSTRAINT;
    }
}