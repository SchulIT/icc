<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class TimetablePeriodVisibility {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="int")
     * @var int
     */
    private $id;

    /**
     * @ORM\Id()
     * @ORM\Column(type="UserType::class")
     * @ORM\OrderBy("asc")
     * @var UserType
     */
    private $userType;

    /**
     * @return int|null
     */
    public function getId(): ?int {
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
     * @return TimetablePeriodVisibility
     */
    public function setUserType(UserType $userType): TimetablePeriodVisibility {
        $this->userType = $userType;
        return $this;
    }
}