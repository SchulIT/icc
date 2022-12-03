<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
class NoReservationCollision extends Constraint {
    public string $messageReservation = 'This room reservation collides with an existing reservation by {{ teacher }} in lesson {{ lessonNumber }}.';
    public string $messageTimetable = 'This room reservation collides with an existing timetable lesson {{ tuition }} ({{ teacher }}) in lesson {{ lessonNumber }}.';
    public string $messageSubstitution = 'This room reservation collides with an existing substitution in lesson {{ lessonNumber }}.';
    public string $messageExam = 'This room reservation collides with an existing exam in lesson {{ lessonNumber }}.';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): array|string {
        return self::CLASS_CONSTRAINT;
    }
}