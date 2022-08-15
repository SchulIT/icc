<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class StudentAbsenceType {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private ?string $name;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private bool $mustApprove = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private bool $isTypeWithZeroAbsenceLessons = false;

    /**
     * @ORM\ManyToMany(targetEntity="UserTypeEntity")
     * @ORM\JoinTable(name="student_absence_type_allowed_usertypes",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<UserTypeEntity>
     */
    private Collection $allowedUserTypes;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->allowedUserTypes = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return StudentAbsenceType
     */
    public function setName(?string $name): StudentAbsenceType {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMustApprove(): bool {
        return $this->mustApprove;
    }

    /**
     * @param bool $mustApprove
     * @return StudentAbsenceType
     */
    public function setMustApprove(bool $mustApprove): StudentAbsenceType {
        $this->mustApprove = $mustApprove;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTypeWithZeroAbsenceLessons(): bool {
        return $this->isTypeWithZeroAbsenceLessons;
    }

    /**
     * @param bool $isTypeWithZeroAbsenceLessons
     * @return StudentAbsenceType
     */
    public function setIsTypeWithZeroAbsenceLessons(bool $isTypeWithZeroAbsenceLessons): StudentAbsenceType {
        $this->isTypeWithZeroAbsenceLessons = $isTypeWithZeroAbsenceLessons;
        return $this;
    }

    public function addAllowedUserType(UserTypeEntity $entity): void {
        $this->allowedUserTypes->add($entity);
    }

    public function removeAllowedUserType(UserTypeEntity $entity): void {
        $this->allowedUserTypes->removeElement($entity);
    }

    public function getAllowedUserTypes(): Collection {
        return $this->allowedUserTypes;
    }

    public function __toString(): string {
        return $this->getName();
    }
}