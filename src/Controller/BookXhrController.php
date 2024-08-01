<?php

namespace App\Controller;

use App\Book\AttendanceSuggestion\AbsenceSuggestionResolver;
use App\Book\AttendanceSuggestion\RemoveSuggestionResolver;
use App\Book\AttendanceSuggestion\SuggestionResolver;
use App\Book\Lesson\LessonCancelHelper;
use App\Book\Student\AbsenceExcuseResolver;
use App\Book\StudentsResolver;
use App\Entity\AttendanceExcuseStatus;
use App\Entity\AttendanceType;
use App\Entity\Grade;
use App\Entity\Attendance;
use App\Entity\AttendanceFlag;
use App\Entity\LessonEntry;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudentAbsence;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\Tuition;
use App\Markdown\Markdown;
use App\Repository\LessonAttendanceFlagRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\StudentAbsenceRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Request\Book\CancelLessonRequest;
use App\Request\Book\UpdateAttendanceRequest;
use App\Response\Book\AttendanceSuggestion;
use App\Response\Book\RemoveSuggestion;
use App\Response\Book\Student as StudentResponse;
use App\Response\Book\StudyGroupStudents;
use App\Response\Book\Teacher as TeacherResponse;
use App\Response\Book\Subject as SubjectResponse;
use App\Response\Book\Tuition as TuitionResponse;
use App\Response\Book\Grade as GradeResponse;
use App\Response\Book\StudyGroup as StudyGroupResponse;
use App\Response\ViolationList;
use App\Section\SectionResolverInterface;
use App\Security\Voter\LessonEntryVoter;
use App\Settings\BookSettings;
use App\Sorting\Sorter;
use App\Sorting\StudyGroupStrategy;
use App\Utils\ArrayUtils;
use DateTime;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
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
            $teachers[] = $this->getTeacher($teacher);
        }

        return $this->returnJson($teachers, $serializer);
    }

    #[Route(path: '/tuition/{uuid}', name: 'xhr_tuition')]
    public function tuition(Tuition $tuition, SerializerInterface $serializer): Response {
        return $this->returnJson($this->getTuition($tuition), $serializer);
    }

    /**
     * @param Tuition $tuition
     * @param DateTime $date
     * @param int $lessonStart
     * @param int $lessonEnd
     * @param SuggestionResolver $suggestionResolver
     * @return AttendanceSuggestion[]
     */
    private function possiblyAbsentStudents(Tuition $tuition, DateTime $date, int $lessonStart, int $lessonEnd, SuggestionResolver $suggestionResolver): array {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        return $suggestionResolver->resolve($tuition, $date, $lessonStart, $lessonEnd);
    }

    /**
     * @param Tuition $tuition
     * @param DateTime $date
     * @param int $lessonStart
     * @param int $lessonEnd
     * @param RemoveSuggestionResolver $removeSuggestionResolver
     * @return RemoveSuggestion[]
     */
    private function removeSuggestions(Tuition $tuition, DateTime $date, int $lessonStart, int $lessonEnd, RemoveSuggestionResolver $removeSuggestionResolver): array {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);
        return $removeSuggestionResolver->resolve($tuition, $date, $lessonStart, $lessonEnd);
    }

    #[Route(path: '/attendances/{uuid}', name: 'xhr_entry_attendances')]
    public function attendances(Request $request, LessonEntry $entry, SerializerInterface $serializer, SectionResolverInterface $sectionResolver): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        $filter = $request->query->get('filter', null);
        $data = [ ];

        /** @var Attendance $attendance */
        foreach($entry->getAttendances() as $attendance) {
            if($filter === null || intval($filter) === $attendance->getType()) {
                $data[] = [
                    'student' => $this->getStudent($attendance->getStudent(), $sectionResolver->getSectionForDate($entry->getLesson()->getDate())),
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
            $students[] = $this->getStudent($studentEntity, $section);
        }

        return $this->returnJson($students, $serializer);
    }

    #[Route(path: '/studygroups', name: 'xhr_studygroups')]
    public function studyGroups(StudyGroupRepositoryInterface $studyGroupRepository, SectionResolverInterface $sectionResolver, SerializerInterface $serializer, Sorter $sorter): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        $section = $sectionResolver->getCurrentSection();
        $studyGroups = $studyGroupRepository->findAllBySection($section);
        $sorter->sort($studyGroups, StudyGroupStrategy::class);

        $students = [ ];

        foreach($studyGroups as $studyGroup) {
            $students[] = new StudyGroupStudents(
                $this->getStudyGroup($studyGroup),
                $this->getStudyGroupStudents($studyGroup)
            );
        }

        return $this->returnJson($students, $serializer);
    }

    #[Route('/absence_note/{student}/{lesson}', name: 'xhr_student_absences')]
    #[ParamConverter('student', options: [ 'mapping' => ['student' => 'uuid']])]
    #[ParamConverter('lesson', options: [ 'mapping' => ['lesson' => 'uuid']])]
    public function absenceNote(Student $student, TimetableLesson $lesson, StudentAbsenceRepositoryInterface $absenceRepository, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, Markdown $markdown): Response {
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

    #[OA\Get]
    #[OA\Parameter(name: 'lesson', description: 'UUID der Stundenplanstunde', in: 'query')]
    #[OA\Parameter(name: 'start', description: 'Start der Unterrichtsstunde', in: 'query')]
    #[OA\Parameter(name: 'end', description: 'Ende der Unterrichtsstunde', in: 'query')]
    #[Route(path: '/entry', name: 'xhr_lesson_entry', methods: ['GET'])]
    public function entry(Request $request, TimetableLessonRepositoryInterface $lessonRepository, SuggestionResolver $suggestionResolver,
                          SerializerInterface $serializer, AbsenceExcuseResolver $excuseResolver, BookSettings $settings,
                          RemoveSuggestionResolver $removeSuggestionResolver, StudentsResolver $studentsResolver, LessonAttendanceFlagRepositoryInterface $attendanceFlagRepository): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        $lesson = $lessonRepository->findOneByUuid($request->query->get('lesson'));
        if($lesson === null) {
            throw new NotFoundHttpException('Lesson not found.');
        }

        $start = $request->query->getInt('start');
        if(!is_numeric($start)) {
            throw new BadRequestHttpException('Start must be numeric values.');
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
            /** @var Attendance $attendance */
            foreach($entry->getAttendances() as $attendance) {
                $excuseInfo = $excuseResolver->resolve($attendance->getStudent(), [ $entry->getTuition() ]);
                $excuses = $excuseInfo->getExcuseCollectionForLesson($attendance);

                $attendances[] = [
                    'student' => $this->getStudent($attendance->getStudent()),
                    'minutes' => $attendance->getLateMinutes(),
                    'lesson' => $attendance->getLesson(),
                    'comment' => $attendance->getComment(),
                    'excuse_status' => $attendance->getExcuseStatus()->value,
                    'zero_absent_lesson' => $attendance->isZeroAbsentLesson(),
                    'has_excuses' => $excuses->count() > 0,
                    'type' => $attendance->getType()->value,
                    'flags' => $attendance->getFlags()->map(fn(AttendanceFlag $flag) => $flag->getId())->toArray()
                ];
            }

            $entryJson = [
                'uuid' => $entry->getUuid()->toString(),
                'start' => $entry->getLessonStart(),
                'end' => $entry->getLessonEnd(),
                'subject' => $this->getSubject($entry->getSubject()),
                'replacement_subject' => $entry->getReplacementSubject(),
                'teacher' => $this->getTeacher($entry->getTeacher()),
                'replacement_teacher' => $this->getTeacher($entry->getReplacementTeacher()),
                'topic' => $entry->getTopic(),
                'exercises' => $entry->getExercises(),
                'comment' => $entry->getComment(),
                'is_cancelled' => $entry->isCancelled(),
                'cancel_reason' => $entry->getCancelReason(),
                'attendances' => $attendances
            ];
        }

        $students = [ ];

        if($entry === null) {
            foreach($studentsResolver->resolve($lesson->getTuition()) as $student) {
                $students[] = $this->getStudent($student);
            }
        } else {
            foreach($entry->getAttendances() as $attendance) {
                $students[] = $this->getStudent($attendance->getStudent());
            }
        }

        $response = [
            'lesson' => [
                'uuid' => $lesson->getUuid()->toString(),
                'date' => $lesson->getDate()->format('Y-m-d'),
                'lesson_start' => $lesson->getLessonStart(),
                'lesson_end' => $lesson->getLessonEnd(),
                'tuition' => $this->getTuition($lesson->getTuition())
            ],
            'absences' => $this->possiblyAbsentStudents($lesson->getTuition(), $lesson->getDate(), $start, $lesson->getLessonEnd(), $suggestionResolver),
            'removals' => $this->removeSuggestions($lesson->getTuition(), $lesson->getDate(), $start, $lesson->getLessonEnd(), $removeSuggestionResolver),
            'entry' => $entryJson,
            'students' => $students,
            'has_other_entries' => count($lesson->getEntries()) > 0,
            'flags' => array_map(fn(AttendanceFlag $flag) => $flag->jsonSerialize(), $attendanceFlagRepository->findAllBySubject($lesson->getTuition()->getSubject())),
        ];

        return $this->returnJson($response, $serializer);
    }

    #[OA\Post]
    #[OA\RequestBody(content: new Model(type: CancelLessonRequest::class))]
    #[OA\Response(response: '201', description: 'Unterrichtsstunden erfolgreich als Entfall markiert.')]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ViolationList::class))]
    #[Route(path: '/cancel/{uuid}', name: 'xhr_cancel_lesson', methods: ['POST'])]
    public function cancelLesson(TimetableLesson $lesson, CancelLessonRequest $request, LessonCancelHelper $lessonCancelHelper): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);
        $reason = $request->getReason();
        $lessonCancelHelper->cancelLesson($lesson, $reason);

        return new Response('', Response::HTTP_CREATED, [
            'Content-Type' => 'application/json'
        ]);
    }


    #[OA\Put]
    #[OA\RequestBody(content: new Model(type: UpdateAttendanceRequest::class))]
    #[OA\Response(response: '200', description: 'Anwesenheit erfolgreich aktualisiert.')]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ViolationList::class))]
    #[Route(path: '/attendance/{uuid}', name: 'xhr_update_attendance', methods: ['PUT'])]
    public function updateAttendance(Attendance $attendance, UpdateAttendanceRequest $request, LessonAttendanceRepositoryInterface $repository): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::Edit, $attendance->getEntry());

        $attendance->setIsZeroAbsentLesson($request->isZeroAbsentLesson());
        $attendance->setLateMinutes($request->getLateMinutes());
        $attendance->setExcuseStatus(AttendanceExcuseStatus::from($request->getExcuseStatus()));
        $attendance->setType(AttendanceType::from($request->getType()));
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

    private function getStudent(Student $student, ?Section $section = null): StudentResponse {
        $grade = null;

        if($section !== null && ($gradeEntity = $student->getGrade($section)) !== null) {
            $grade = new GradeResponse($gradeEntity->getUuid()->toString(), $gradeEntity->getName());
        }

        return new StudentResponse(
            $student->getUuid()->toString(),
            $student->getFirstname(),
            $student->getLastname(),
            $grade
        );
    }

    private function getTeacher(?Teacher $teacher): ?TeacherResponse {
        if($teacher === null) {
            return null;
        }

        return new TeacherResponse(
            $teacher->getUuid()->toString(),
            $teacher->getAcronym(),
            $teacher->getFirstname(),
            $teacher->getLastname(),
            $teacher->getTitle());
    }

    private function getSubject(Subject $subject): SubjectResponse {
        return new SubjectResponse(
            $subject->getUuid()->toString(),
            $subject->getName(),
            $subject->getAbbreviation()
        );
    }

    private function getStudyGroup(StudyGroup $studyGroup): StudyGroupResponse {
        return new StudyGroupResponse(
            $studyGroup->getUuid()->toString(),
            $studyGroup->getName(),
            $studyGroup->getType()->value,
            array_map(fn(Grade $grade) => new GradeResponse($grade->getUuid()->toString(), $grade->getName()), $studyGroup->getGrades()->toArray())
        );
    }

    /**
     * @param StudyGroup $studyGroup
     * @return StudentResponse[]
     */
    private function getStudyGroupStudents(StudyGroup $studyGroup): array {
        return array_map(fn(StudyGroupMembership $membership) => $this->getStudent($membership->getStudent(), $studyGroup->getSection()), $studyGroup->getMemberships()->toArray());
    }

    private function getTuition(Tuition $tuition): TuitionResponse {
        return new TuitionResponse(
            $tuition->getUuid()->toString(),
            $tuition->getName(),
            $this->getSubject($tuition->getSubject()),
            $this->getStudyGroup($tuition->getStudyGroup()),
            array_map(fn(Teacher $teacher) => $this->getTeacher($teacher), $tuition->getTeachers()->toArray())
        );
    }
}