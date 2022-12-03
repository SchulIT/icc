<?php

namespace App\Entity;

use Stringable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class UserTypeEntity implements Stringable {

    use IdTrait;

    #[ORM\Column(type: 'user_type', unique: true)]
    private ?UserType $userType = null;

    public function getUserType(): UserType {
        return $this->userType;
    }

    public function setUserType(UserType $userType): UserTypeEntity {
        $this->userType = $userType;
        return $this;
    }

    public function __toString(): string {
        return $this->getUserType()->getKey();
    }
}