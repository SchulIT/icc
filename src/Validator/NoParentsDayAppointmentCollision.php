<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
class NoParentsDayAppointmentCollision extends Constraint {

    public $studentMessage = 'Student {{ student }} has already an appointment in this time slot.';

    public $teacherMessage = 'Teacher {{ teacher }} has already an appointment in this time slot.';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): array|string {
        return self::CLASS_CONSTRAINT;
    }
}