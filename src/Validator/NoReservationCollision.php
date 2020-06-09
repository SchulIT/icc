<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class NoReservationCollision extends Constraint {
    public $messageReservation = 'This room reservation collides with an existing reservation by {{ teacher }} in lesson {{ lessonNumber }}.';
    public $messageTimetable = 'This room reservation collides with an existing timetable lesson {{ tuition }} ({{ teacher }}) in lesson {{ lessonNumber }}.';
    public $messageSubstitution = 'This room reservation collides with an existing substitution in lesson {{ lessonNumber }}.';

    /**
     * {@inheritdoc}
     */
    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }
}