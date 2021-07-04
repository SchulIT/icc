<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradeMembershipData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $student;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $grade;

    /**
     * @return string
     */
    public function getStudent(): string {
        return $this->student;
    }

    /**
     * @param string $student
     * @return GradeMembershipData
     */
    public function setStudent(string $student): GradeMembershipData {
        $this->student = $student;
        return $this;
    }

    /**
     * @return string
     */
    public function getGrade(): string {
        return $this->grade;
    }

    /**
     * @param string $grade
     * @return GradeMembershipData
     */
    public function setGrade(string $grade): GradeMembershipData {
        $this->grade = $grade;
        return $this;
    }
}