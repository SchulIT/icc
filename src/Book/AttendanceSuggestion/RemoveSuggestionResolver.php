<?php

namespace App\Book\AttendanceSuggestion;

use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Response\Book\RemoveSuggestion;
use App\Response\Book\Student as StudentResponse;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

class RemoveSuggestionResolver {

    public function __construct(private readonly TimetableLessonRepositoryInterface $timetableLessonRepository, private readonly TranslatorInterface $translator) {

    }

    public function resolve(Tuition $tuition, DateTime $date, int $lesson): array {
        $students = $tuition->getStudyGroup()->getMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudent())->toArray();

        $suggestions = [ ];

        /** @var Student $student */
        foreach($students as $student) {
            $lessons = $this->timetableLessonRepository->findAllByStudent($date, $date, $student);

            foreach($lessons as $timetableLesson) {
                if($timetableLesson->getLessonStart() > $lesson || $timetableLesson->getLessonEnd() < $lesson) {
                    continue;
                }

                if($timetableLesson->getTuition() !== null && $timetableLesson->getTuition()->getId() !== $tuition->getId()) {
                    $teachers = implode(
                        ', ',
                        $timetableLesson->getTuition()->getTeachers()->map(fn(Teacher $teacher) => $teacher->getAcronym())->toArray()
                    );

                    $suggestions[] = new RemoveSuggestion(
                        new StudentResponse($student->getUuid()->toString(), $student->getFirstname(), $student->getLastname()),
                        $this->translator->trans('book.attendance.remove_reason.timetable', [
                            '%student%' => $student->getFirstname(),
                            '%tuition%' => $timetableLesson->getTuition()->getName(),
                            '%teacher%' => $teachers
                        ])
                    );
                }
            }
        }

        return $suggestions;
    }
}