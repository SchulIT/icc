<?php

namespace App\Event;


use App\Entity\Substitution;
use App\Entity\Teacher;
use Symfony\Contracts\EventDispatcher\Event;

class SubstitutionMentionCreatedEvent extends Event {
    public function __construct(private readonly Substitution $substitution, private readonly Teacher $teacher) {

    }

    public function getSubstitution(): Substitution {
        return $this->substitution;
    }

    public function getTeacher(): Teacher {
        return $this->teacher;
    }
}