<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Section {

    /**
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("year")
     * @var int
     */
    private $year = 0;

    /**
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("number")
     * @var int
     */
    private $number = 0;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("name")
     * @var string
     */
    private $name;

    /**
     * @return int
     */
    public function getYear(): int {
        return $this->year;
    }

    /**
     * @param int $year
     * @return Section
     */
    public function setYear(int $year): Section {
        $this->year = $year;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumber(): int {
        return $this->number;
    }

    /**
     * @param int $number
     * @return Section
     */
    public function setNumber(int $number): Section {
        $this->number = $number;
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
     * @return Section
     */
    public function setName(string $name): Section {
        $this->name = $name;
        return $this;
    }
}