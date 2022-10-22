<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Section {

    /**
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("year")
     */
    private int $year = 0;

    /**
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("number")
     */
    private int $number = 0;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("name")
     */
    private ?string $name = null;

    public function getYear(): int {
        return $this->year;
    }

    public function setYear(int $year): Section {
        $this->year = $year;
        return $this;
    }

    public function getNumber(): int {
        return $this->number;
    }

    public function setNumber(int $number): Section {
        $this->number = $number;
        return $this;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): Section {
        $this->name = $name;
        return $this;
    }
}