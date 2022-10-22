<?php

namespace App\Entity;

use Stringable;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class StudyGroupMembership implements Stringable {

    use IdTrait;

    /**
     * @ORM\ManyToOne(targetEntity="StudyGroup", inversedBy="memberships")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?StudyGroup $studyGroup = null;

    /**
     * @ORM\ManyToOne(targetEntity="Student", inversedBy="studyGroupMemberships")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?Student $student = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $type = null;

    public function getStudyGroup(): StudyGroup {
        return $this->studyGroup;
    }

    public function setStudyGroup(StudyGroup $studyGroup): StudyGroupMembership {
        $this->studyGroup = $studyGroup;
        return $this;
    }

    public function getStudent(): Student {
        return $this->student;
    }

    public function setStudent(Student $student): StudyGroupMembership {
        $this->student = $student;
        return $this;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(?string $type): StudyGroupMembership {
        $this->type = $type;
        return $this;
    }

    public function __toString(): string {
        return sprintf('%s [%s]', $this->getStudent(), $this->getType());
    }
}