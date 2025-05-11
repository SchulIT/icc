<?php

namespace App\Request;

use Attribute;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;

#[Attribute(Attribute::TARGET_PARAMETER)]
class JsonPayload extends ValueResolver {
    public function __construct(
        public readonly bool $validate = true,
        public readonly ?string $version = null,
        public readonly ?array $groups = null
    ) {
        parent::__construct('json');
    }
}