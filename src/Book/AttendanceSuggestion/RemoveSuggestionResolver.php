<?php

namespace App\Book\AttendanceSuggestion;

use App\Book\StudentsResolver;
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

    use StudentTransformerTrait;

    public function __construct(private readonly TimetableLessonRepositoryInterface $timetableLessonRepository, private readonly StudentsResolver $studentsResolver, private readonly TranslatorInterface $translator) {

    }

    public function resolve(Tuition $tuition, DateTime $date, int $lesson): array {
        $students = $this->studentsResolver->resolve($tuition);

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
                        $this->getStudent($student),
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