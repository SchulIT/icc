<?php

namespace App\Framework\Security\Firewall\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class IsGrantedIfNotImpersonated {

}