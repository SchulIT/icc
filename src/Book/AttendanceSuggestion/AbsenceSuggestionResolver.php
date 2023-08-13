<?php

namespace App\Book\AttendanceSuggestion;

use App\Dashboard\Absence\AbsenceResolver;
use App\Dashboard\AbsentExamStudent;
use App\Dashboard\AbsentStudent;
use App\Dashboard\AbsentStudentWithAbsenceNote;
use App\Entity\LessonAttendanceExcuseStatus;
use App\Entity\Student as StudentEntity;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Response\Book\AbsenceSuggestion;
use App\Response\Book\Student;
use DateTime;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AbsenceSuggestionResolver {
    public function __construct(private readonly AbsenceResolver $absenceResolver, private readonly LessonAttendanceRepositoryInterface $attendanceRepository,
                                private readonly ExcuseNoteRepositoryInterface $excuseNoteRepository, private readonly UrlGeneratorInterface $urlGenerator) {

    }

    /**
     * @param Tuition $tuition
     * @param DateTime $date
     * @param int $lesson
     * @return AbsenceSuggestion[]
     */
    public function resolve(Tuition $tuition, DateTime $date, int $lesson): array {
        $students = $tuition->getStudyGroup()->getMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudent())->toArray();

        $suggestions = [ ];

        $absences = $this->absenceResolver->resolve($date, $lesson, $students);

        foreach($this->filterAbsencesWithAreExcused($absences) as $absentStudent) {
            if(array_key_exists($absentStudent->getStudent()->getId(), $suggestions)) {
                continue; // prevent duplicates
            }

            $excuseStatus = ($absentStudent instanceof AbsentStudentWithAbsenceNote && $absentStudent->getAbsence()->getType()->isAlwaysExcused()) ? LessonAttendanceExcuseStatus::Excused : LessonAttendanceExcuseStatus::NotSet;

            $suggestions[$absentStudent->getStudent()->getId()] = new AbsenceSuggestion(
                $this->getStudent($absentStudent->getStudent()),
                $absentStudent->getReason()->value,
                $absentStudent->getAbsence()->getType()->getName(),
                $absentStudent->getAbsence()->getType()->isTypeWithZeroAbsenceLessons(),
                $excuseStatus,
                $this->urlGenerator->generate('show_student_absence', [ 'uuid' => $absentStudent->getAbsence()->getUuid() ], UrlGeneratorInterface::ABSOLUTE_URL)
            );
        }

        foreach($this->filterAbsencesWithoutZeroAbsenceLessons($absences) as $absentStudent) {
            if(array_key_exists($absentStudent->getStudent()->getId(), $suggestions)) {
                continue; // prevent duplicates
            }

            $excuseStatus = ($absentStudent instanceof AbsentStudentWithAbsenceNote && $absentStudent->getAbsence()->getType()->isAlwaysExcused()) ? LessonAttendanceExcuseStatus::Excused : LessonAttendanceExcuseStatus::NotSet;

            $suggestions[$absentStudent->getStudent()->getId()] = new AbsenceSuggestion(
                $this->getStudent($absentStudent->getStudent()),
                $absentStudent->getReason()->value,
                $absentStudent->getAbsence()->getType()->getName(),
                false,
                $excuseStatus,
                $this->urlGenerator->generate('show_student_absence', [ 'uuid' => $absentStudent->getAbsence()->getUuid() ], UrlGeneratorInterface::ABSOLUTE_URL)
            );
        }

        // Absence in previous lesson
        foreach($this->attendanceRepository->findAbsentByStudentsAndDate($students, $date) as $attendance) {
            if(array_key_exists($attendance->getStudent()->getId(), $suggestions)) {
                continue; // prevent duplicates
            }

            if($attendance->getEntry()->getLessonEnd() === $lesson - 1) {
                $suggestions[$attendance->getStudent()->getId()] = new AbsenceSuggestion(
                    $this->getStudent($attendance->getStudent()),
                    'absent_before'
                );
            }
        }

        // Exam
        foreach($this->filterAbsencesWithExam($absences) as $absentExamStudent) {
            if(array_key_exists($absentExamStudent->getStudent()->getId(), $suggestions)) {
                continue; // prevent duplicates
            }

            if($absentExamStudent->getExam()->getTuitions()->count() === 1 && $absentExamStudent->getExam()->getTuitions()->first() === $tuition) {
                continue; // do not show exam of current tuition
            }

            $suggestions[$absentExamStudent->getStudent()->getId()] = new AbsenceSuggestion(
                $this->getStudent($absentExamStudent->getStudent()),
                $absentExamStudent->getReason()->value,
                null,
                true,
                LessonAttendanceExcuseStatus::Excused
            );
        }

        // Absences with zero absent lessons
        foreach($this->filterAbsencesWithZeroAbsenceLessons($absences) as $absentStudent) {
            if(array_key_exists($absentStudent->getStudent()->getId(), $suggestions)) {
                continue; // prevent duplicates
            }

            $excuseStatus = ($absentStudent instanceof AbsentStudentWithAbsenceNote && $absentStudent->getAbsence()->getType()->isAlwaysExcused()) ? LessonAttendanceExcuseStatus::Excused : LessonAttendanceExcuseStatus::NotSet;

            $suggestions[$absentStudent->getStudent()->getId()] = new AbsenceSuggestion(
                $this->getStudent($absentStudent->getStudent()),
                $absentStudent->getReason()->value,
                $absentStudent->getAbsence()->getType()->getName(),
                true,
                $excuseStatus,
                $this->urlGenerator->generate('show_student_absence', [ 'uuid' => $absentStudent->getAbsence()->getUuid() ], UrlGeneratorInterface::ABSOLUTE_URL)
            );
        }

        // Excuse
        foreach($this->excuseNoteRepository->findByStudentsAndDate($students, $date) as $note) {
            if(array_key_exists($note->getStudent()->getId(), $suggestions)) {
                continue; // prevent duplicates
            }

            if($note->appliesToLesson($date, $lesson)) {
                $suggestions[$note->getStudent()->getId()] = new AbsenceSuggestion(
                    $this->getStudent($note->getStudent()),
                    'excuse',
                    null,
                    false,
                    LessonAttendanceExcuseStatus::Excused
                );
            }
        }

        return array_values($suggestions);
    }

    /**
     * @param AbsentStudent[] $students
     * @return AbsentStudentWithAbsenceNote[]
     */
    private function filterAbsencesWithoutZeroAbsenceLessons(array $students): array {
        return array_filter($students, fn(AbsentStudent $absentStudent) => ($absentStudent instanceof AbsentStudentWithAbsenceNote && !$absentStudent->getAbsence()->getType()->isTypeWithZeroAbsenceLessons()));
    }

    /**
     * @param AbsentStudent[] $students
     * @return AbsentExamStudent[]
     */
    private function filterAbsencesWithExam(array $students): array {
        return array_filter($students, fn(AbsentStudent $absentStudent) => $absentStudent instanceof AbsentExamStudent);
    }

    /**
     * @param AbsentStudent[] $students
     * @return AbsentStudentWithAbsenceNote[]
     */
    private function filterAbsencesWithZeroAbsenceLessons(array $students): array {
        return array_filter($students, fn(AbsentStudent $absentStudent) => ($absentStudent instanceof AbsentStudentWithAbsenceNote && $absentStudent->getAbsence()->getType()->isTypeWithZeroAbsenceLessons()));
    }

    /**
     * @param AbsentStudent[] $students
     * @return AbsentStudentWithAbsenceNote[]
     */
    private function filterAbsencesWithAreExcused(array $students): array {
        return array_filter($students, fn(AbsentStudent $absentStudent) => $absentStudent instanceof AbsentStudentWithAbsenceNote && $absentStudent->getAbsence()->getType()->isAlwaysExcused());
    }

    private function getStudent(StudentEntity $entity): Student {
        return new Student($entity->getUuid()->toString(), $entity->getFirstname(), $entity->getLastname());
    }
}