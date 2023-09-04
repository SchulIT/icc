<?php

namespace App\Entity;

use Stringable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class StudentAbsenceType implements Stringable {

    use IdTrait;
    use UuidTrait;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private ?string $name = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $details = null;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private bool $mustApprove = false;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isTypeWithZeroAbsenceLessons = false;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isAlwaysExcused = false;

    #[ORM\Column(type: 'json')]
    #[Assert\All([
        new Assert\NotBlank(),
        new Assert\Email()
    ])]
    private array $additionalRecipients = [ ];

    /**
     * @var Collection<UserTypeEntity>
     */
    #[ORM\JoinTable(name: 'student_absence_type_allowed_usertypes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: UserTypeEntity::class)]
    private Collection $allowedUserTypes;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->allowedUserTypes = new ArrayCollection();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): StudentAbsenceType {
        $this->name = $name;
        return $this;
    }

    public function getDetails(): ?string {
        return $this->details;
    }

    public function setDetails(?string $details): StudentAbsenceType {
        $this->details = $details;
        return $this;
    }

    public function isMustApprove(): bool {
        return $this->mustApprove;
    }

    public function setMustApprove(bool $mustApprove): StudentAbsenceType {
        $this->mustApprove = $mustApprove;
        return $this;
    }

    public function isTypeWithZeroAbsenceLessons(): bool {
        return $this->isTypeWithZeroAbsenceLessons;
    }

    public function setIsTypeWithZeroAbsenceLessons(bool $isTypeWithZeroAbsenceLessons): StudentAbsenceType {
        $this->isTypeWithZeroAbsenceLessons = $isTypeWithZeroAbsenceLessons;
        return $this;
    }

    public function isAlwaysExcused(): bool {
        return $this->isAlwaysExcused;
    }

    public function setIsAlwaysExcused(bool $isAlwaysExcused): StudentAbsenceType {
        $this->isAlwaysExcused = $isAlwaysExcused;
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

    /**
     * @return string[]
     */
    public function getAdditionalRecipients(): array {
        return $this->additionalRecipients;
    }

    /**
     * @param string[] $additionalRecipients
     */
    public function setAdditionalRecipients(array $additionalRecipients): void {
        $this->additionalRecipients = $additionalRecipients;
    }

    public function __toString(): string {
        return $this->getName();
    }
}