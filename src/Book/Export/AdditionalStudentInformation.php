<?php

namespace App\Book\Export;

use DateTime;
use JMS\Serializer\Annotation as Serializer;

class AdditionalStudentInformation {

    #[Serializer\Type(Student::class)]
    #[Serializer\SerializedName('student')]
    private Student $student;

    #[Serializer\Type('DateTime')]
    #[Serializer\SerializedName('from')]
    private DateTime $from;

    #[Serializer\Type('DateTime')]
    #[Serializer\SerializedName('until')]
    private DateTime $until;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('content')]
    private string $content;

    public function getStudent(): Student {
        return $this->student;
    }

    public function setStudent(Student $student): AdditionalStudentInformation {
        $this->student = $student;
        return $this;
    }

    public function getFrom(): DateTime {
        return $this->from;
    }

    public function setFrom(DateTime $from): AdditionalStudentInformation {
        $this->from = $from;
        return $this;
    }

    public function getUntil(): DateTime {
        return $this->until;
    }

    public function setUntil(DateTime $until): AdditionalStudentInformation {
        $this->until = $until;
        return $this;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function setContent(string $content): AdditionalStudentInformation {
        $this->content = $content;
        return $this;
    }
}