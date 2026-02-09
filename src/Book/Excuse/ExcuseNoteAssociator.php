<?php

namespace App\Book\Excuse;

use App\Entity\Attendance;
use App\Entity\AttendanceType;
use App\Entity\ExcuseNote;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;

readonly class ExcuseNoteAssociator {
    public function __construct(
        private LessonAttendanceRepositoryInterface $attendanceRepository,
        private ExcuseNoteRepositoryInterface $excuseNoteRepository
    ) {

    }

    public function removeUnusedAssociatedAttendances(ExcuseNote $excuseNote): void {
        $toRemove = [ ];
        foreach($excuseNote->getAssociatedAttendances() as $attendance) {
            if(!$excuseNote->appliesToLesson($attendance->getDate(), $attendance->getLesson())) {
                $toRemove[] = $attendance;
            }
        }

        foreach($toRemove as $attendance) {
            $excuseNote->removeAssociatedAttendance($attendance);
        }

        $this->excuseNoteRepository->persist($excuseNote);
    }

    public function associateExcuseNote(ExcuseNote $excuseNote): void {
        $this->removeUnusedAssociatedAttendances($excuseNote);

        $attendances = $this->attendanceRepository->findByStudent(
            $excuseNote->getStudent(),
            $excuseNote->getFrom()->getDate(),
            $excuseNote->getUntil()->getDate(),
            true
        );

        foreach($attendances as $attendance) {
            if($excuseNote->appliesToLesson($attendance->getDate(), $attendance->getLesson())
                && !$attendance->getAssociatedExcuses()->contains($excuseNote)) {
                $attendance->addAssociatedExcuse($excuseNote);
                $this->attendanceRepository->persist($attendance);
            }
        }
    }

    public function associateAttendance(Attendance $attendance): void {
        if($attendance->getType() !== AttendanceType::Absent || $attendance->isZeroAbsentLesson()) {
            return;
        }
        $excuseNotes = $this->excuseNoteRepository->findByStudentsAndDate([ $attendance->getStudent() ], $attendance->getDate());

        foreach($excuseNotes as $excuseNote) {
            if($excuseNote->appliesToLesson($attendance->getDate(), $attendance->getLesson()) && !$attendance->getAssociatedExcuses()->contains($excuseNote)) {
                $attendance->addAssociatedExcuse($excuseNote);
            }
        }

        $this->attendanceRepository->persist($attendance);
    }
}