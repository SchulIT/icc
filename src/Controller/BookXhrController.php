<?php

namespace App\Controller;

use App\Book\Lesson\LessonCancelHelper;
use App\Book\Student\AbsenceExcuseResolver;
use App\Dashboard\Absence\AbsenceResolver;
use App\Dashboard\AbsenceReason;
use App\Dashboard\AbsentStudentWithAbsenceNote;
use App\Entity\LessonAttendance;
use App\Entity\LessonAttendanceExcuseStatus;
use App\Entity\LessonEntry;
use App\Entity\StudyGroupMembership;
use App\Entity\TimetableLesson;
use App\Entity\Tuition;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Request\Book\CancelLessonRequest;
use App\Request\Book\UpdateAttendanceRequest;
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

#[Route(path: '/book/xhr')]
class BookXhrController extends AbstractController {

    use DateRequestTrait;

    private function returnJson($data, SerializerInterface $serializer): Response {
        $json = $serializer->serialize($data, 'json');
        return new Response($json, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route(path: '/teachers', name: 'xhr_teachers')]
    public function teachers(TeacherRepositoryInterface $teacherRepository, SerializerInterface $serializer): Response {
        $teachers = [];

        foreach($teacherRepository->findAll() as $teacher) {
            $teachers[] = Teacher::fromEntity($teacher);
        }

        return $this->returnJson($teachers, $serializer);
    }

    #[Route(path: '/tuition/{uuid}', name: 'xhr_tuition')]
    public function tuition(Tuition $tuition, SerializerInterface $serializer): Response {
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
            $zeroAbsentLessons = ($absentStudent instanceof  AbsentStudentWithAbsenceNote && $absentStudent->getAbsence()->getType()->isTypeWithZeroAbsenceLessons());
            $excuseStatus = ($absentStudent instanceof AbsentStudentWithAbsenceNote && $absentStudent->getAbsence()->getType()->isAlwaysExcused()) ? LessonAttendanceExcuseStatus::Excused : LessonAttendanceExcuseStatus::NotSet;

            if($absentStudent->getReason()->equals(AbsenceReason::Exam())) {
                $zeroAbsentLessons = true;
                $excuseStatus = LessonAttendanceExcuseStatus::Excused;
            }

            $absences[] = [
                'student' => Student::fromEntity($absentStudent->getStudent(), $sectionResolver->getCurrentSection()),
                'reason' => $absentStudent->getReason()->getValue(),
                'label' => ($absentStudent instanceof AbsentStudentWithAbsenceNote ? $absentStudent->getAbsence()->getType()->getName() : null),
                'zero_absent_lessons' => $zeroAbsentLessons,
                'excuse_status' => $excuseStatus
            ];
        }

        foreach($excuseNoteRepository->findByStudentsAndDate($students, $date) as $note) {
            if($note->appliesToLesson($date, $lesson)) {
                $absences[] = [
                    'student' => Student::fromEntity($note->getStudent(), $sectionResolver->getCurrentSection()),
                    'reason' => 'excuse',
                    'excuse_status' => LessonAttendanceExcuseStatus::Excused
                ];
            }
        }

        if(empty($absences)) {
            foreach ($attendanceRepository->findAbsentByStudentsAndDate($students, $date) as $attendance) {
                if ($attendance->getEntry()->getLessonEnd() < $lesson) {
                    $absences[] = [
                        'student' => Student::fromEntity($attendance->getStudent(), $sectionResolver->getCurrentSection()),
                        'reason' => 'absent_before'
                    ];
                    break; // only show this absence once
                }
            }
        }

        return $absences;
    }

    #[Route(path: '/attendances/{uuid}', name: 'xhr_entry_attendances')]
    public function attendances(Request $request, LessonEntry $entry, SerializerInterface $serializer, SectionResolverInterface $sectionResolver): Response {
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

    #[Route(path: '/students', name: 'xhr_students')]
    public function students(StudentRepositoryInterface $studentRepository, SectionResolverInterface $sectionResolver, SerializerInterface $serializer): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        $students = [ ];
        $section = $sectionResolver->getCurrentSection();

        foreach($studentRepository->findAllBySection($section) as $studentEntity) {
            $students[] = Student::fromEntity($studentEntity, $section);
        }

        return $this->returnJson($students, $serializer);
    }

    /**
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
    #[Route(path: '/entry', name: 'xhr_lesson_entry', methods: ['GET'])]
    public function entry(Request $request, AbsenceResolver $absenceResolver, TimetableLessonRepositoryInterface $lessonRepository,
                          LessonAttendanceRepositoryInterface $attendanceRepository, ExcuseNoteRepositoryInterface $excuseNoteRepository,
                          SerializerInterface $serializer, SectionResolverInterface $sectionResolver, AbsenceExcuseResolver $excuseResolver): Response {
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
            if($lessonEntry->getLessonStart() <= (int)$start && (int)$start <= $lessonEntry->getLessonEnd()) {
                $entry = $lessonEntry;
                break;
            }
        }

        $entryJson = null;

        if($entry !== null) {
            $attendances = [ ];
            /** @var LessonAttendance $attendance */
            foreach($entry->getAttendances() as $attendance) {
                $excuseInfo = $excuseResolver->resolve($attendance->getStudent(), [ $entry->getTuition() ]);
                $excuses = $excuseInfo->getExcuseCollectionForLesson($attendance);

                $attendances[] = [
                    'student' => Student::fromEntity($attendance->getStudent()),
                    'minutes' => $attendance->getLateMinutes(),
                    'lessons' => $attendance->getAbsentLessons(),
                    'comment' => $attendance->getComment(),
                    'excuse_status' => $attendance->getExcuseStatus(),
                    'has_excuses' => $excuses->count() > 0,
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
     * @OA\Post()
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
    #[Route(path: '/cancel/{uuid}', name: 'xhr_cancel_lesson', methods: ['POST'])]
    public function cancelLesson(TimetableLesson $lesson, CancelLessonRequest $request, LessonCancelHelper $lessonCancelHelper): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);
        $reason = $request->getReason();
        $lessonCancelHelper->cancelLesson($lesson, $reason);

        return new Response('', Response::HTTP_CREATED, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @OA\Put()
     * @OA\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=UpdateAttendanceRequest::class)
     * )
     * @OA\Response(
     *     response="200",
     *     description="Attendance successfully updated"
     * )
     * @OA\Response(
     *     response="403",
     *     description="Bad request.",
     *     @Model(type=ViolationList::class)
     * )
     */
    #[Route(path: '/attendance/{uuid}', name: 'xhr_update_attendance', methods: ['PUT'])]
    public function updateAttendance(LessonAttendance $attendance, UpdateAttendanceRequest $request, LessonAttendanceRepositoryInterface $repository): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::Edit, $attendance->getEntry());

        $attendance->setAbsentLessons($request->getAbsentLessons());
        $attendance->setLateMinutes($request->getLateMinutes());
        $attendance->setExcuseStatus($request->getExcuseStatus());
        $attendance->setType($request->getType());
        $attendance->setComment($request->getComment());

        $repository->persist($attendance);

        return new Response('', Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }
}