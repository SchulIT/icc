<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class MessageVisibility {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="UserType::class")
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
     * @return MessageVisibility
     */
    public function setUserType(UserType $userType): MessageVisibility {
        $this->userType = $userType;
        return $this;
    }

}