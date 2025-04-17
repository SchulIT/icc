<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[Auditable]
class ReturnItem {
    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private Student $student;

    #[ORM\ManyToOne(targetEntity: ReturnItemType::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    #[Assert\NotNull]
    private ReturnItemType $type;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTime $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Gedmo\Blameable(on: 'create')]
    private ?User $createdBy;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isReturned = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Gedmo\Timestampable(on: 'change', field: 'isReturned')]
    private ?DateTime $returnedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Gedmo\Blameable(on: 'change', field: 'isReturned')]
    private ?User $returnedBy = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $returnComment = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getStudent(): Student {
        return $this->student;
    }

    public function setStudent(Student $student): void {
        $this->student = $student;
    }

    public function getType(): ReturnItemType {
        return $this->type;
    }

    public function setType(ReturnItemType $type): void {
        $this->type = $type;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function getCreatedBy(): ?User {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): void {
        $this->createdBy = $createdBy;
    }

    public function isReturned(): bool {
        return $this->isReturned;
    }

    public function setIsReturned(bool $isReturned): void {
        $this->isReturned = $isReturned;
    }

    public function getReturnedAt(): ?DateTime {
        return $this->returnedAt;
    }

    public function setReturnedAt(?DateTime $returnedAt): void {
        $this->returnedAt = $returnedAt;
    }

    public function getReturnedBy(): ?User {
        return $this->returnedBy;
    }

    public function setReturnedBy(?User $returnedBy): void {
        $this->returnedBy = $returnedBy;
    }

    public function getReturnComment(): ?string {
        return $this->returnComment;
    }

    public function setReturnComment(?string $returnComment): void {
        $this->returnComment = $returnComment;
    }
}