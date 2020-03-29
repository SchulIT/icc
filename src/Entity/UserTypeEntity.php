<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class UserTypeEntity {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="user_type", unique=true)
     * @var UserType
     */
    private $userType;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

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
}