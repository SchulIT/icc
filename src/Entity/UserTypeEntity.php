<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class UserTypeEntity {

    use IdTrait;

    /**
     * @ORM\Column(type="user_type", unique=true)
     * @var UserType
     */
    private $userType;

    /**
     * @return UserType
     */
    public function getUserType(): UserType {
        return $this->userType;
    }

    /**
     * @param UserType $userType
     * @return UserTypeEntity
     */
    public function setUserType(UserType $userType): UserTypeEntity {
        $this->userType = $userType;
        return $this;
    }

    public function __toString() {
        return $this->getUserType()->getKey();
    }
}