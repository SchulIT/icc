<?php

namespace App\Book\AttendanceSuggestion;

use App\Book\StudentsResolver;
use App\Common\Entity\Student;
use App\Common\Entity\StudyGroupMembership;
use App\Common\Entity\Teacher;
use App\Common\Entity\Tuition;
use App\Timetable\Repository\TimetableLessonRepositoryInterface;
use App\Book\Xhr\Response\RemoveSuggestion;
use App\Book\Xhr\Response\Student as StudentResponse;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

class RemoveSuggestionResolver {

    use StudentTransformerTrait;

    public function __construct(private readonly TimetableLessonRepositoryInterface $timetableLessonRepository, private readonly StudentsResolver $studentsResolver, private readonly TranslatorInterface $translator) {

    }

    public function resolve(Tuition $tuition, DateTime $date, int $lessonStart, int $lessonEnd): array {
        $students = $this->studentsResolver->resolve($tuition);

        $suggestions = [ ];

        /** @var Student $student */
        foreach($students as $student) {
            $lessons = $this->timetableLessonRepository->findAllByStudent($date, $date, $student);

            foreach($lessons as $timetableLesson) {
                $intersection = array_intersect(
                    range($lessonStart, $lessonEnd),
                    range($timetableLesson->getLessonStart(), $timetableLesson->getLessonEnd()),
                );

                if(count($intersection) === 0) {
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
                        ]),
                        $intersection
                    );
                }
            }
        }

        return $suggestions;
    }
}