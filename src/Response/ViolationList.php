<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ViolationList extends ErrorResponse {

    /**
     * @param Violation[] $violations
     */
    public function __construct(/**
     * List of violations
     * @Serializer\Type("array<App\Response\Violation>")
     * @Serializer\SerializedName("violations")
     */
    private array $violations) {
        parent::__construct('Validation failed.');
    }

    /**
     * @return Violation[]
     */
    public function getViolations(): array {
        return $this->violations;
    }
}