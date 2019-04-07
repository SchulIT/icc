<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class WikiArticleVisibility {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="UserType::class", unique=true)
     * @ORM\OrderBy("asc")
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
     * @return WikiArticleVisibility
     */
    public function setUserType(UserType $userType): WikiArticleVisibility {
        $this->userType = $userType;
        return $this;
    }
}