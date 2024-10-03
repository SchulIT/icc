<?php

namespace App\Entity;

use App\Validator\Color;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class ChatTag {
    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 24)]
    private ?string $name;

    #[ORM\Column(type: 'string')]
    #[Color]
    private ?string $color;

    /**
     * @var ArrayCollection<UserTypeEntity>
     */
    #[ORM\JoinTable(name: 'chat_tag_usertypes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: UserTypeEntity::class)]
    private Collection $userTypes;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->userTypes = new ArrayCollection();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): ChatTag {
        $this->name = $name;
        return $this;
    }

    public function getColor(): ?string {
        return $this->color;
    }

    public function setColor(?string $color): ChatTag {
        $this->color = $color;
        return $this;
    }

    public function addUserType(UserTypeEntity $userType): void {
        $this->userTypes->add($userType);
    }

    public function removeUserType(UserTypEEntity $userType): void {
        $this->userTypes->removeElement($userType);
    }

    public function getUserTypes(): Collection {
        return $this->userTypes;
    }
}