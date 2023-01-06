<?php

namespace App\Book\AbsenceSuggestion;

use App\Dashboard\Absence\AbsenceResolver;
use App\Dashboard\AbsentExamStudent;
use App\Dashboard\AbsentStudent;
use App\Dashboard\AbsentStudentWithAbsenceNote;
use App\Entity\LessonAttendanceExcuseStatus;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Response\Api\V1\Student;
use App\Section\SectionResolverInterface;
use DateTime;

class SuggestionResolver {
    public function __construct(private readonly AbsenceResolver $absenceResolver, private readonly LessonAttendanceRepositoryInterface $attendanceRepository,
                                private readonly ExcuseNoteRepositoryInterface $excuseNoteRepository, private readonly SectionResolverInterface $sectionResolver) {

    }

    public function resolve(Tuition $tuition, DateTime $date, int $lesson) {
        $students = $tuition->getStudyGroup()->getMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudent())->toArray();

        $suggestions = [ ];

        $absences = $this->absenceResolver->resolve($date, $lesson, $students);

        foreach($this->filterAbsencesWithoutZeroAbsenceLessons($absences) as $absentStudent) {
            if(array_key_exists($absentStudent->getStudent()->getId(), $suggestions)) {
                continue; // prevent duplicates
            }

            $excuseStatus = ($absentStudent instanceof AbsentStudentWithAbsenceNote && $absentStudent->getAbsence()->getType()->isAlwaysExcused()) ? LessonAttendanceExcuseStatus::Excused : LessonAttendanceExcuseStatus::NotSet;

            $suggestions[$absentStudent->getStudent()->getId()] = [
                'student' => Student::fromEntity($absentStudent->getStudent(), $this->sectionResolver->getCurrentSection()),
                'reason' => $absentStudent->getReason()->value,
                'label' => $absentStudent->getAbsence()->getType()->getName(),
                'zero_absent_lessons' => false,
                'excuse_status' => $excuseStatus
            ];
        }

        // Absence in previous lesson
        foreach($this->attendanceRepository->findAbsentByStudentsAndDate($students, $date) as $attendance) {
            if(array_key_exists($attendance->getStudent()->getId(), $suggestions)) {
                continue; // prevent duplicates
            }

            if($attendance->getEntry()->getLessonEnd() < $lesson) {
                $suggestions[$attendance->getStudent()->getId()] = [
                    'student' => Student::fromEntity($attendance->getStudent(), $this->sectionResolver->getCurrentSection()),
                    'reason' => 'absent_before'
                ];
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

            $suggestions[$absentExamStudent->getStudent()->getId()] = [
                'student' => Student::fromEntity($absentExamStudent->getStudent(), $this->sectionResolver->getCurrentSection()),
                'reason' => $absentExamStudent->getReason()->value,
                'zero_absent_lessons' => true,
                'excuse_status' => LessonAttendanceExcuseStatus::Excused
            ];
        }

        // Absences with zero absent lessons
        foreach($this->filterAbsencesWithZeroAbsenceLessons($absences) as $absentStudent) {
            if(array_key_exists($absentStudent->getStudent()->getId(), $suggestions)) {
                continue; // prevent duplicates
            }

            $excuseStatus = ($absentStudent instanceof AbsentStudentWithAbsenceNote && $absentStudent->getAbsence()->getType()->isAlwaysExcused()) ? LessonAttendanceExcuseStatus::Excused : LessonAttendanceExcuseStatus::NotSet;

            $suggestions[$absentStudent->getStudent()->getId()] = [
                'student' => Student::fromEntity($absentStudent->getStudent(), $this->sectionResolver->getCurrentSection()),
                'reason' => $absentStudent->getReason()->value,
                'label' => $absentStudent->getAbsence()->getType()->getName(),
                'zero_absent_lessons' => true,
                'excuse_status' => $excuseStatus
            ];
        }

        // Excuse
        foreach($this->excuseNoteRepository->findByStudentsAndDate($students, $date) as $note) {
            if(array_key_exists($note->getStudent()->getId(), $suggestions)) {
                continue; // prevent duplicates
            }

            if($note->appliesToLesson($date, $lesson)) {
                $suggestions[$note->getStudent()->getId()] = [
                    'student' => Student::fromEntity($note->getStudent(), $this->sectionResolver->getCurrentSection()),
                    'reason' => 'excuse',
                    'excuse_status' => LessonAttendanceExcuseStatus::Excused
                ];
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
}