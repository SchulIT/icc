<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ViolationList extends ErrorResponse {

    /**
     * List of violations
     * @Serializer\Type("array<App\Response\Violation>")
     * @Serializer\SerializedName("violations")
     * @var Violation[]
     */
    private $violations = [ ];

    /**
     * @param Violation[] $violations
     */
    public function __construct(array $violations) {
        parent::__construct('Validation failed.');

        $this->violations = $violations;
    }

    /**
     * @return Violation[]
     */
    public function getViolations(): array {
        return $this->violations;
    }
}