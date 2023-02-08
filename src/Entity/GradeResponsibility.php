<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class GradeResponsibility {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: Grade::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?Grade $grade;

    #[ORM\ManyToOne(targetEntity: Section::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?Section $section;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $task;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $person;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return Grade|null
     */
    public function getGrade(): ?Grade {
        return $this->grade;
    }

    /**
     * @param Grade|null $grade
     * @return GradeResponsibility
     */
    public function setGrade(?Grade $grade): GradeResponsibility {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return Section|null
     */
    public function getSection(): ?Section {
        return $this->section;
    }

    /**
     * @param Section|null $section
     * @return GradeResponsibility
     */
    public function setSection(?Section $section): GradeResponsibility {
        $this->section = $section;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTask(): ?string {
        return $this->task;
    }

    /**
     * @param string|null $task
     * @return GradeResponsibility
     */
    public function setTask(?string $task): GradeResponsibility {
        $this->task = $task;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPerson(): ?string {
        return $this->person;
    }

    /**
     * @param string|null $person
     * @return GradeResponsibility
     */
    public function setPerson(?string $person): GradeResponsibility {
        $this->person = $person;
        return $this;
    }
}