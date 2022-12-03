<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class FileExtension extends Constraint {
    /** @var string[] */
    public array $extensions;

    public string $message = 'File has the wrong format. Expected Format: {{ extensions }}';

    public function __construct(mixed $options = null, array $groups = null, mixed $payload = null, ?array $extensions = null) {
        parent::__construct($options, $groups, $payload);

        if($extensions !== null) {
            $this->extensions = $extensions;
        }
    }
}