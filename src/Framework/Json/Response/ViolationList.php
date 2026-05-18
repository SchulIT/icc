<?php

namespace App\Framework\Json\Response;

use App\Framework\Json\Response\ErrorResponse;
use App\Framework\Json\Response\Violation;
use JMS\Serializer\Annotation as Serializer;

class ViolationList extends ErrorResponse {

    /**
     * @param Violation[] $violations
     */
    public function __construct(
        /**
         * List of violations
         */
        #[Serializer\Type('array<' . Violation::class . '>')]
        #[Serializer\SerializedName('violations')]
        private array $violations
    ) {
        parent::__construct('Validation failed.');
    }

    /**
     * @return Violation[]
     */
    public function getViolations(): array {
        return $this->violations;
    }
}