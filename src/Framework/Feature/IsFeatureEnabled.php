<?php

namespace App\Framework\Feature;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
readonly class IsFeatureEnabled {

    public function __construct(public Feature $feature) {

    }
}