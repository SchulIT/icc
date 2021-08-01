<?php

namespace App\Controller;

use App\Dashboard\Absence\AbsenceResolver;
use App\Entity\Lesson;
use App\Entity\LessonAttendance;
use App\Entity\LessonEntry;
use App\Entity\Student as StudentEntity;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\LessonEntryRepositoryInterface;
use App\Repository\LessonRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Request\Book\CancelLessonRequest;
use App\Request\ValidationFailedException;
use App\Response\Api\V1\Student;
use App\Response\Api\V1\Teacher;
use App\Response\Api\V1\Tuition as TuitionResponse;
use App\Response\ViolationList;
use App\Section\SectionResolverInterface;
use App\Security\Voter\LessonEntryVoter;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/book/xhr")
 */
class BookXhrController extends AbstractController {

    use DateRequestTrait;

    private function returnJson($data, SerializerInterface $serializer): Response {
        $json = $serializer->serialize($data, 'json');
        return new Response($json, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/teachers", name="xhr_teachers")
     */
    public function teachers(TeacherRepositoryInterface $teacherRepository, SerializerInterface $serializer) {
        $teachers = [];

        foreach($teacherRepository->findAll() as $teacher) {
            $teachers[] = Teacher::fromEntity($teacher);
        }

        return $this->returnJson($teachers, $serializer);
    }

    /**
     * @Route("/tuition/{uuid}", name="xhr_tuition")
     */
    public function tuition(Tuition $tuition, SerializerInterface $serializer) {
        return $this->returnJson(TuitionResponse::fromEntity($tuition), $serializer);
    }

    /**
     * @Route("/tuition/{uuid}/students", name="xhr_tuition_students")
     */
    public function possiblyAbsentStudents(Tuition $tuition, Request $request, AbsenceResolver $absenceResolver,
                                           LessonAttendanceRepositoryInterface $attendanceRepository, ExcuseNoteRepositoryInterface $excuseNoteRepository,
                                           SerializerInterface $serializer, SectionResolverInterface $sectionResolver) {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        $date = $this->getDateFromRequest($request, 'date');

        if($date === null) {
            throw new BadRequestHttpException('date must not be null.');
        }

        $lesson = $request->query->getInt('lesson', 0);

        if($lesson <= 0) {
            throw new BadRequestHttpException('lesson must be greater than 0');
        }

        $students = [ ];

        /** @var StudyGroupMembership $membership */
        foreach($tuition->getStudyGroup()->getMemberships() as $membership) {
            $students[] = $membership->getStudent();
        }

        $absences = [ ];

        foreach($absenceResolver->resolve($date, $lesson, $students) as $absentStudent) {
            $absences[] = [
                'student' => Student::fromEntity($absentStudent->getStudent(), $sectionResolver->getCurrentSection()),
                'reason' => $absentStudent->getReason()->getValue()
            ];
        }

        foreach($attendanceRepository->findAbsentByStudents($students, $date) as $attendance) {
            if($attendance->getEntry()->getLessonEnd() < $lesson) {
                $absences[] = [
                    'student' => Student::fromEntity($attendance->getStudent(), $sectionResolver->getCurrentSection()),
                    'reason' => 'absent_before'
                ];
            }
        }

        foreach($excuseNoteRepository->findByStudentsAndDate($students, $date) as $note) {
            if($note->getLessonStart() <= $lesson && $note->getLessonEnd() >= $lesson) {
                $absences[] = [
                    'student' => Student::fromEntity($note->getStudent(), $sectionResolver->getCurrentSection()),
                    'reason' => 'excuse'
                ];
            }
        }

        $response = [
            'students' => array_map(function(StudentEntity $student) use($sectionResolver) {
                return Student::fromEntity($student, $sectionResolver->getCurrentSection());
            }, $students),
            'absent' => $absences
        ];

        return $this->returnJson($response, $serializer);
    }

    /**
     * @Route("/attendances/{uuid}", name="xhr_entry_attendances")
     */
    public function attendances(LessonEntry $entry, SerializerInterface $serializer, SectionResolverInterface $sectionResolver) {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        $data = [ ];

        /** @var LessonAttendance $attendance */
        foreach($entry->getAttendances() as $attendance) {
            $data[] = [
                'student' => Student::fromEntity($attendance->getStudent(), $sectionResolver->getCurrentSection()),
                'type' => $attendance->getType()
            ];
        }

        return $this->returnJson($data, $serializer);
    }

    /**
     * @Route("/students", name="xhr_students")
     */
    public function students(StudentRepositoryInterface $studentRepository, SectionResolverInterface $sectionResolver, SerializerInterface $serializer) {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        $students = [ ];
        $section = $sectionResolver->getCurrentSection();

        foreach($studentRepository->findAllBySection($section) as $studentEntity) {
            $students[] = Student::fromEntity($studentEntity, $section);
        }

        return $this->returnJson($students, $serializer);
    }

    /**
     * @Route("/cancel/{uuid}", name="xhr_cancel_lesson", methods={"POST"})
     * @OA\POST()
     * @OA\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=CancelLessonRequest::class)
     * )
     * @OA\Response(
     *     response="201",
     *     description="Lessons are cancelled successfully. Empty content."
     * )
     * @OA\Response(
     *     response="403",
     *     description="Bad request.",
     *     @Model(type=ViolationList::class)
     * )
     */
    public function cancelLesson(Lesson $lesson, CancelLessonRequest $request, LessonEntryRepositoryInterface $entryRepository) {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);
        $tuition = $lesson->getTuition();

        if($lesson->getEntries()->count() === 0) {
            $entry = (new LessonEntry())
                ->setLesson($lesson)
                ->setTuition($tuition)
                ->setLessonStart($lesson->getLessonStart())
                ->setLessonEnd($lesson->getLessonEnd())
                ->setIsCancelled(true)
                ->setTeacher($tuition->getTeachers()->first())
                ->setSubject($tuition->getSubject())
                ->setCancelReason($request->getReason());

            $entryRepository->persist($entry);
        } else {
            $lessonNumbers = range($lesson->getLessonStart(), $lesson->getLessonEnd());

            /** @var LessonEntry $entry */
            foreach ($lesson->getEntries() as $entry) {
                for ($lessonNumber = $entry->getLessonStart(); $lessonNumber <= $entry->getLessonEnd(); $lessonNumber++) {
                    unset($lessonNumbers[$lessonNumber]);
                }
            }

            foreach ($lessonNumbers as $lessonNumber) {
                $entry = (new LessonEntry())
                    ->setLesson($lesson)
                    ->setTuition($tuition)
                    ->setLessonStart($lessonNumber)
                    ->setLessonEnd($lessonNumber)
                    ->setIsCancelled(true)
                    ->setTeacher($tuition->getTeachers()->first())
                    ->setSubject($tuition->getSubject())
                    ->setCancelReason($request->getReason());

                $entryRepository->persist($entry);
            }
        }

        return new Response('', Response::HTTP_CREATED, [
            'Content-Type' => 'application/json'
        ]);
    }
}