<?php

namespace App\Controller;

use App\Book\Lesson\LessonCancelHelper;
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
use App\Response\Api\V1\Subject;
use App\Response\Api\V1\Teacher;
use App\Response\Api\V1\Tuition as TuitionResponse;
use App\Response\ViolationList;
use App\Section\SectionResolverInterface;
use App\Security\Voter\LessonEntryVoter;
use DateTime;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    private function possiblyAbsentStudents(Tuition $tuition, DateTime $date, int $lesson, AbsenceResolver $absenceResolver,
                                            LessonAttendanceRepositoryInterface $attendanceRepository, ExcuseNoteRepositoryInterface $excuseNoteRepository,
                                            SectionResolverInterface $sectionResolver) {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

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
            if($note->appliesToLesson($date, $lesson)) {
                $absences[] = [
                    'student' => Student::fromEntity($note->getStudent(), $sectionResolver->getCurrentSection()),
                    'reason' => 'excuse'
                ];
            }
        }

        return $absences;
    }

    /**
     * @Route("/attendances/{uuid}", name="xhr_entry_attendances")
     */
    public function attendances(Request $request, LessonEntry $entry, SerializerInterface $serializer, SectionResolverInterface $sectionResolver) {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        $filter = $request->query->get('filter', null);
        $data = [ ];

        /** @var LessonAttendance $attendance */
        foreach($entry->getAttendances() as $attendance) {
            if($filter === null || intval($filter) === $attendance->getType()) {
                $data[] = [
                    'student' => Student::fromEntity($attendance->getStudent(), $sectionResolver->getCurrentSection()),
                    'type' => $attendance->getType()
                ];
            }
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
     * @Route("/entry", name="xhr_lesson_entry", methods={"GET"})
     * @OA\Get()
     * @OA\Parameter(
     *     name="lesson",
     *     in="query",
     *     description="UUID of the lesson"
     * )
     * @OA\Parameter(
     *     name="start",
     *     in="query",
     *     description="Start lesson number"
     * )
     * @OA\Parameter(
     *     name="end",
     *     in="path",
     *     description="End lesson number"
     * )
     */
    public function entry(Request $request, AbsenceResolver $absenceResolver, LessonRepositoryInterface $lessonRepository,
                          LessonAttendanceRepositoryInterface $attendanceRepository, ExcuseNoteRepositoryInterface $excuseNoteRepository,
                          SerializerInterface $serializer, SectionResolverInterface $sectionResolver) {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        $lesson = $lessonRepository->findOneByUuid($request->query->get('lesson'));
        if($lesson === null) {
            throw new NotFoundHttpException('Lesson not found.');
        }

        $start = $request->query->getInt('start');
        if(!is_numeric($start)) {
            throw new BadRequestHttpException('Start and end must be numeric values.');
        }

        if($start < $lesson->getLessonStart() || $start > $lesson->getLessonEnd()) {
            throw new BadRequestHttpException('Start must be inside lesson boundaries.');
        }

        $entry = null;

        /** @var LessonEntry $lessonEntry */
        foreach($lesson->getEntries() as $lessonEntry) {
            if($lessonEntry->getLessonStart() === (int)$start) {
                $entry = $lessonEntry;
                break;
            }
        }

        $entryJson = null;

        if($entry !== null) {
            $attendances = [ ];
            /** @var LessonAttendance $attendance */
            foreach($entry->getAttendances() as $attendance) {
                $attendances[] = [
                    'student' => Student::fromEntity($attendance->getStudent()),
                    'minutes' => $attendance->getLateMinutes(),
                    'lessons' => $attendance->getAbsentLessons(),
                    'comment' => $attendance->getComment(),
                    'excuse_status' => $attendance->getExcuseStatus(),
                    'type' => $attendance->getType()
                ];
            }

            $entryJson = [
                'uuid' => $entry->getUuid()->toString(),
                'start' => $entry->getLessonStart(),
                'end' => $entry->getLessonEnd(),
                'subject' => Subject::fromEntity($entry->getSubject()),
                'replacement_subject' => $entry->getReplacementSubject(),
                'teacher' => Teacher::fromEntity($entry->getTeacher()),
                'replacement_teacher' => Teacher::fromEntity($entry->getReplacementTeacher()),
                'topic' => $entry->getTopic(),
                'exercises' => $entry->getExercises(),
                'comment' => $entry->getComment(),
                'is_cancelled' => $entry->isCancelled(),
                'cancel_reason' => $entry->getCancelReason(),
                'attendances' => $attendances
            ];
        }

        $students = [ ];
        foreach($lesson->getTuition()->getStudyGroup()->getMemberships() as $membership) {
            $students[] = Student::fromEntity($membership->getStudent());
        }

        $response = [
            'lesson' => [
                'uuid' => $lesson->getUuid()->toString(),
                'date' => $lesson->getDate()->format('Y-m-d'),
                'lesson_start' => $lesson->getLessonStart(),
                'lesson_end' => $lesson->getLessonEnd(),
                'tuition' => TuitionResponse::fromEntity($lesson->getTuition())
            ],
            'absences' => $this->possiblyAbsentStudents($lesson->getTuition(), $lesson->getDate(), $start, $absenceResolver, $attendanceRepository, $excuseNoteRepository, $sectionResolver),
            'entry' => $entryJson,
            'students' => $students,
            'has_other_entries' => count($lesson->getEntries()) > 0
        ];

        return $this->returnJson($response, $serializer);
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
    public function cancelLesson(Lesson $lesson, CancelLessonRequest $request, LessonCancelHelper $lessonCancelHelper) {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);
        $reason = $request->getReason();
        $lessonCancelHelper->cancelLesson($lesson, $reason);

        return new Response('', Response::HTTP_CREATED, [
            'Content-Type' => 'application/json'
        ]);
    }
}