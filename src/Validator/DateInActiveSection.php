<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DateInActiveSection extends Constraint {
    public $message = 'This date must be inside the active section ({{ start }}-{{ end }}).';
}