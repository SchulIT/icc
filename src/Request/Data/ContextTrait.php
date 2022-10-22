<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;

trait ContextTrait {
    /**
     * Optional context. Currently only supports a date (YYYY-MM-DD) as input (or null). If set,
     * only substitutions for the given date are imported. Substitutions of other dates are ignored and thus preserved
     * (both at import level and when removing old one).
     * @Serializer\Type("string")
     * @var string|null
     */
    private ?string $context = null;

    public function getContext(): ?string {
        return $this->context;
    }

    public function setContext(?string $context): self {
        $this->context = $context;
        return $this;
    }
}