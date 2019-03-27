<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\CommonMark\Util\ArrayCollection;
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
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Grade
     */
    public function setId(int $id): Grade {
        $this->id = $id;
        return $this;
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
     * @return ArrayCollection<Student>
     */
    public function getStudents(): ArrayCollection {
        return $this->students;
    }

}