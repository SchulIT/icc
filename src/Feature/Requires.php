<?php

namespace App\Feature;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class Requires {
    public function __construct(public array $features = [ ], public Requirement $requirement = Requirement::All) {

    }
}