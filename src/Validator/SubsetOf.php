<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\AbstractComparison;

/**
 * @Annotation
 */
class SubsetOf extends AbstractComparison {
    public $message = 'This should be a subset of {{ compared_value_path }}.';
}