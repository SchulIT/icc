<?php

namespace App\Book\Entity;

use App\Common\Entity\IdTrait;
use App\Common\Entity\SectionAwareTrait;
use App\Common\Entity\Student;
use App\Common\Entity\User;
use App\Common\Entity\UuidTrait;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[Auditable]
class ReportRemark {

    use IdTrait;
    use UuidTrait;
    use SectionAwareTrait;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private Student|null $student = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private string|null $remark = null;

    #[Gedmo\Blameable(on: 'create')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $createdBy = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getStudent(): ?Student {
        return $this->student;
    }

    public function setStudent(?Student $student): ReportRemark {
        $this->student = $student;
        return $this;
    }

    public function getRemark(): ?string {
        return $this->remark;
    }

    public function setRemark(?string $remark): ReportRemark {
        $this->remark = $remark;
        return $this;
    }

    public function getCreatedBy(): ?User {
        return $this->createdBy;
    }
}