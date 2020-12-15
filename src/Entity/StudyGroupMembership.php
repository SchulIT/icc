<?php

namespace App\Entity;

use DH\DoctrineAuditBundle\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class StudyGroupMembership {

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="StudyGroup", inversedBy="memberships")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var StudyGroup
     */
    private $studyGroup;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Student", inversedBy="studyGroupMemberships")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Student
     */
    private $student;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $type;

    /**
     * @return StudyGroup
     */
    public function getStudyGroup(): StudyGroup {
        return $this->studyGroup;
    }

    /**
     * @param StudyGroup $studyGroup
     * @return StudyGroupMembership
     */
    public function setStudyGroup(StudyGroup $studyGroup): StudyGroupMembership {
        $this->studyGroup = $studyGroup;
        return $this;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student {
        return $this->student;
    }

    /**
     * @param Student $student
     * @return StudyGroupMembership
     */
    public function setStudent(Student $student): StudyGroupMembership {
        $this->student = $student;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return StudyGroupMembership
     */
    public function setType(?string $type): StudyGroupMembership {
        $this->type = $type;
        return $this;
    }

    public function __toString() {
        return sprintf('%s [%s]', $this->getStudent(), $this->getType());
    }
}