<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class AppointmentVisibility {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
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
     * @return AppointmentVisibility
     */
    public function setUserType(UserType $userType): AppointmentVisibility {
        $this->userType = $userType;
        return $this;
    }
}