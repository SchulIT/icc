<?php

namespace App\Controller;

use App\Book\AbsenceSuggestion\SuggestionResolver;
use App\Book\Lesson\LessonCancelHelper;
use App\Book\Student\AbsenceExcuseResolver;
use App\Entity\LessonAttendance;
use App\Entity\LessonEntry;
use App\Entity\Student as StudentEntity;
use App\Entity\StudentAbsence;
use App\Entity\TimetableLesson;
use App\Entity\Tuition;
use App\Markdown\Markdown;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\StudentAbsenceRepositoryInterface;
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
use App\Settings\BookSettings;
use App\Utils\ArrayUtils;
use DateTime;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    private function possiblyAbsentStudents(Tuition $tuition, DateTime $date, int $lesson, SuggestionResolver $suggestionResolver) {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        return $suggestionResolver->resolve($tuition, $date, $lesson);
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

    #[Route('/absence_note/{student}/{lesson}', name: 'xhr_student_absences')]
    #[ParamConverter('student', options: [ 'mapping' => ['student' => 'uuid']])]
    #[ParamConverter('lesson', options: [ 'mapping' => ['lesson' => 'uuid']])]
    public function absenceNote(StudentEntity $student, TimetableLesson $lesson, StudentAbsenceRepositoryInterface $absenceRepository, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, Markdown $markdown): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        $absences = [ ];
        for($lessonNumber = $lesson->getLessonStart(); $lessonNumber <= $lesson->getLessonEnd(); $lessonNumber++) {
            $absences = array_merge(
                $absences,
                $absenceRepository->findByStudents([$student], null, $lesson->getDate(), $lessonNumber)
            );
        }

        $absences = ArrayUtils::unique($absences);
        $json = array_map(fn(StudentAbsence $absence) => [
            'uuid' => $absence->getUuid()->toString(),
            'type' => $absence->getType()->getName(),
            'from' => [
                'date' => $absence->getFrom()->getDate()->format('Y-m-d'),
                'lesson' => $absence->getFrom()->getLesson()
            ],
            'until' => [
                'date' => $absence->getUntil()->getDate()->format('Y-m-d'),
                'lesson' => $absence->getUntil()->getLesson()
            ],
            'message' => $absence->getMessage(),
            'html' => $markdown->convertToHtml($absence->getMessage()),
            'url' => $urlGenerator->generate('show_student_absence', [ 'uuid' => $absence->getUuid()->toString() ])
        ], $absences);

        return $this->returnJson($json, $serializer);
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
    public function entry(Request $request, TimetableLessonRepositoryInterface $lessonRepository, SuggestionResolver $suggestionResolver,
                          SerializerInterface $serializer, AbsenceExcuseResolver $excuseResolver, BookSettings $settings): Response {
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
            if(in_array($membership->getStudent()->getStatus(), $settings->getExcludeStudentsStatus())) {
                // skip student
                continue;
            }

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
            'absences' => $this->possiblyAbsentStudents($lesson->getTuition(), $lesson->getDate(), $start, $suggestionResolver),
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

    #[Route(path: '/font/regular', name: 'xhr_font_regular')]
    public function regularFont(BookSettings $bookSettings) {
        $font = $bookSettings->getRegularFont();

        if(empty($font)) {
            throw new NotFoundHttpException();
        }

        return new Response($font);
    }

    #[Route(path: '/font/bold', name: 'xhr_font_bold')]
    public function boldFont(BookSettings $bookSettings) {
        $font = $bookSettings->getBoldFont();

        if(empty($font)) {
            throw new NotFoundHttpException();
        }

        return new Response($font);
    }
}