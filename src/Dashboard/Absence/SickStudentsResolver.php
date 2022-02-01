<?php

namespace App\Dashboard\Absence;

use App\Dashboard\AbsentSickStudent;
use App\Entity\SickNote;
use App\Repository\SickNoteRepositoryInterface;
use App\Utils\ArrayUtils;
use DateTime;

class SickStudentsResolver implements AbsenceResolveStrategyInterface {

    private $sickNoteRepository;

    public function __construct(SickNoteRepositoryInterface $sickNoteRepository) {
        $this->sickNoteRepository = $sickNoteRepository;
    }

    /**
     * @inheritDoc
     */
    public function resolveAbsentStudents(DateTime $dateTime, int $lesson, iterable $students): array {
        $students = ArrayUtils::iterableToArray($students);
        $sickNotes = $this->sickNoteRepository->findByStudents($students, null, $dateTime, $lesson);

        return array_map(function(SickNote $note) {
            return new AbsentSickStudent($note->getStudent(), $note);
        }, $sickNotes);
    }
}