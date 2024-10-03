<?php

namespace App\Security\Firewall\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class IsGrantedIfNotImpersonated {

}