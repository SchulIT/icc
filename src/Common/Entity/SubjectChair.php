<?php

namespace App\Common\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[Auditable]
#[ORM\Entity]
class SubjectChair {

    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: 'chairs')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    public Subject $subject;

    #[ORM\ManyToOne(targetEntity: Teacher::class, inversedBy: 'chairs')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    public Teacher $teacher;

    #[ORM\Column(type: Types::STRING, enumType: ChairType::class)]
    public ChairType $chairType = ChairType::Primary;

    public function getSubject(): Subject {
        return $this->subject;
    }

    public function setSubject(Subject $subject): SubjectChair {
        $this->subject = $subject;
        return $this;
    }

    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher): SubjectChair {
        $this->teacher = $teacher;
        return $this;
    }

    public function getChairType(): ChairType {
        return $this->chairType;
    }

    public function setChairType(ChairType $chairType): SubjectChair {
        $this->chairType = $chairType;
        return $this;
    }
}
