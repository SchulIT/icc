<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Responsibility {

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('task')]
    private string $task;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('person')]
    private string $person;

    public function getTask(): string {
        return $this->task;
    }

    public function setTask(string $task): Responsibility {
        $this->task = $task;
        return $this;
    }

    public function getPerson(): string {
        return $this->person;
    }

    public function setPerson(string $person): Responsibility {
        $this->person = $person;
        return $this;
    }
}