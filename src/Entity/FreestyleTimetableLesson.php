<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class FreestyleTimetableLesson extends TimetableLesson {
    /**
     * @ORM\ManyToMany(targetEntity="Teacher")
     * @ORM\JoinTable()
     * @var Collection<Teacher>
     */
    private $teachers;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $subject;

    public function __construct() {
        parent::__construct();

        $this->teachers = new ArrayCollection();
    }

    /**
     * @param Teacher $teacher
     */
    public function addTeacher(Teacher $teacher): void {
        $this->teachers->add($teacher);
    }

    /**
     * @param Teacher $teacher
     */
    public function removeTeacher(Teacher $teacher): void {
        $this->teachers->removeElement($teacher);
    }

    /**
     * @return Collection<Teacher>
     */
    public function getTeachers(): Collection {
        return $this->teachers;
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     * @return TimetableLesson
     */
    public function setSubject(?string $subject): TimetableLesson {
        $this->subject = $subject;
        return $this;
    }
}