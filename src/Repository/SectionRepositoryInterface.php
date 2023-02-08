<?php

namespace App\Repository;

use App\Entity\Section;
use DateTime;

interface SectionRepositoryInterface {
    public function findOneByDate(DateTime $dateTime): ?Section;

    public function findOneByNumberAndYear(int $number, int $year): ?Section;

    public function findOneById(int $id): ?Section;

    public function findOneByUuid(string $uuid): ?Section;

    /**
     * @return Section[]
     */
    public function findAll(): array;

    public function persist(Section $section): void;

    public function remove(Section $section): void;
}