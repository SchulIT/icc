<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class FileExtension extends Constraint {
    /** @var string[] */
    public array $extensions;

    public string $message = 'File has the wrong format. Expected Format: {{ extensions }}';
}