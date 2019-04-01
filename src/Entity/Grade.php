<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Grade {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @var string
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Student", mappedBy="grade")
     * @var ArrayCollection<Student>
     */
    private $students;

    public function __construct() {
        $this->students = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Grade
     */
    public function setName(string $name): Grade {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<Student>
     */
    public function getStudents(): Collection {
        return $this->students;
    }

}