<?php

namespace App\StudentAbsence;

use App\Book\Entity\ExcuseNote;
use App\StudentAbsence\Entity\StudentAbsence;
use App\Common\Entity\Teacher;
use App\Book\Repository\ExcuseNoteRepositoryInterface;

readonly class AssociatedExcuseNoteManager {

    public const string Pattern = 'student_absence:%s';

    public function __construct(
        private ExcuseNoteRepositoryInterface $excuseNoteRepository
    ) { }

    private function getComment(StudentAbsence $absence): string {
        return sprintf(self::Pattern, $absence->getUuid()->toString());
    }

    public function getAssociatedExcuseNotes(StudentAbsence $absence): array {
        return $this->excuseNoteRepository->findByStudentAndComment(
            $absence->getStudent(),
            $this->getComment($absence)
        );
    }

    public function createOrUpdateExcuseNote(StudentAbsence $absence, Teacher $teacher): void {
        $comment = $this->getComment($absence);
        $existingNotes = $this->getAssociatedExcuseNotes($absence);

        if(count($existingNotes) === 0) {
            $excuseNote = (new ExcuseNote())
                ->setStudent($absence->getStudent())
                ->setComment($comment);
        } else {
            $excuseNote = array_shift($existingNotes);
        }

        $excuseNote
            ->setFrom($absence->getFrom())
            ->setUntil($absence->getUntil())
            ->setExcusedBy($teacher);

        $this->excuseNoteRepository->persist($excuseNote);
    }
}