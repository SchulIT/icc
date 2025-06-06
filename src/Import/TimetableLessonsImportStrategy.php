<?php

namespace App\Import;

use App\Entity\Grade;
use App\Entity\Room;
use App\Entity\Section;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\Tuition;
use App\Repository\GradeRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use App\Repository\SubjectRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Request\Data\TimetableLessonData;
use App\Request\Data\TimetableLessonsData;
use App\Section\SectionResolverInterface;
use App\Utils\ArrayUtils;
use App\Utils\CollectionUtils;
use Psr\Log\LoggerInterface;
use SchulIT\CommonBundle\Helper\DateHelper;

class TimetableLessonsImportStrategy implements ReplaceImportStrategyInterface, InitializeStrategyInterface, PostActionStrategyInterface {

    private array $gradeCache;
    private array $teacherCache;
    private array $roomCache;
    private array $subjectCache;
    private array $tuitionCache = [ ];

    public function __construct(private readonly TimetableLessonRepositoryInterface $timetableRepository, private readonly TuitionRepositoryInterface $tuitionRepository,
                                private readonly RoomRepositoryInterface $roomRepository, private readonly TeacherRepositoryInterface $teacherRepository,
                                private readonly SubjectRepositoryInterface $subjectRepository, private readonly GradeRepositoryInterface $gradeRepository,
                                private readonly SectionResolverInterface $sectionResolver, private readonly LoggerInterface $logger, private readonly DateHelper $dateHelper)
    {
    }

    public function initialize($requestData): void {
        $this->gradeCache = ArrayUtils::createArrayWithKeys(
            $this->gradeRepository->findAll(),
            fn(Grade $grade) => $grade->getExternalId()
        );

        $this->teacherCache = ArrayUtils::createArrayWithKeys(
            $this->teacherRepository->findAll(),
            fn(Teacher $teacher) => $teacher->getAcronym()
        );

        $this->subjectCache = ArrayUtils::createArrayWithKeys(
            $this->subjectRepository->findAll(),
            fn(Subject $subject) => $subject->getAbbreviation()
        );

        $this->roomCache = ArrayUtils::createArrayWithKeys(
            $this->roomRepository->findAllExternal(),
            fn(Room $room) => $room->getExternalId()
        );
    }

    /**
     * @param string[] $grades
     * @param string[] $teachers
     * @return Tuition[]
     */
    private function findTuition(array $grades, array $teachers, string $subjectOrCourse, Section $section): array {
        sort($grades);
        sort($teachers);

        $key = sprintf(
            '%d-%s-%s-%s',
            $section->getId(),
            implode('~', $grades),
            implode('~', $teachers),
            $subjectOrCourse
        );

        if(!isset($this->tuitionCache[$key])) {
            $this->tuitionCache[$key] = $this->tuitionRepository->findAllByGradeTeacherAndSubjectOrCourse($grades, $teachers, $subjectOrCourse, $section);
        }

        return $this->tuitionCache[$key];
    }

    /**
     * @param TimetableLessonData $data
     * @param TimetableLessonsData $requestData
     * @throws ImportException
     */
    public function persist($data, $requestData): void {
        $entity = new TimetableLesson();

        if($data->getDate() < $requestData->getStartDate() || $data->getDate() > $requestData->getEndDate()) {
            return;
        }

        if($data->getDate()->setTime(0, 0, 0) <= $this->dateHelper->getToday()) {
            return; // ignore all timetable entries from the past
        }

        $section = $this->sectionResolver->getSectionForDate($data->getDate());

        if($section === null) {
            throw new SectionNotResolvableException($data->getDate());
        }

        if(!empty($data->getSubject()) && count($data->getGrades()) > 0 && count($data->getTeachers()) > 0) {
            $tuitions = $this->findTuition($data->getGrades(), $data->getTeachers(), $data->getSubject(), $section);

            if (count($tuitions) === 0) {
                $entity->setTuition(null);
            } else {
                if (count($tuitions) === 1) {
                    $entity->setTuition(array_shift($tuitions));
                } else {
                    $entity->setTuition(null);
                }
            }
        } else {
            $entity->setTuition(null);
        }

        if(!empty($data->getRoom())) {
            //$room = $this->roomRepository->findOneByExternalId($data->getRoom());
            $room = $this->roomCache[$data->getRoom()] ?? null;
            $entity->setRoom($room);

            if($room === null) {
                $entity->setLocation($data->getRoom());
            }
        } else {
            $entity->setRoom(null);
        }

        if($data->getSubject() !== null) {
            //$subject = $this->subjectRepository->findOneByAbbreviation($data->getSubject());
            $subject = $this->subjectCache[$data->getSubject()] ?? null;
            $entity->setSubject($subject);
        }

        $entity->setSubjectName($data->getSubject());

        if($entity->getTuition() === null && $entity->getSubject() === null) {
            $this->logger->info(sprintf(
                'Kein Unterricht für die Stundenplanstunde mit dem Fach "%s", den Lehrkräften "%s" und Klasse(n) "%s" gefunden.',
                $data->getSubject(),
                implode(',', $data->getTeachers()),
                implode(',', $data->getGrades())
            ));
        }

        CollectionUtils::synchronize(
            $entity->getGrades(),
            ArrayUtils::findAllWithKeys($this->gradeCache, $data->getGrades()),
            //$this->gradeRepository->findAllByExternalId($data->getGrades()),
            fn(Grade $grade) => $grade->getId()
        );

        CollectionUtils::synchronize(
            $entity->getTeachers(),
            ArrayUtils::findAllWithKeys($this->teacherCache, $data->getTeachers()),
            //$teachers = $this->teacherRepository->findAllByExternalId($data->getTeachers()),
            fn(Teacher $teacher) => $teacher->getId()
        );

        $entity->setExternalId($data->getId());
        $entity->setDate($data->getDate());
        $entity->setLessonStart($data->getLessonStart());
        $entity->setLessonEnd($data->getLessonEnd());

        $this->timetableRepository->persist($entity);
    }


    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->timetableRepository;
    }

    /**
     * @param TimetableLessonsData $data
     * @return TimetableLessonData[]
     */
    public function getData($data): array {
        return $data->getLessons();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return TimetableLesson::class;
    }

    /**
     * @param TimetableLessonsData $data
     */
    public function removeAll($data): void {
        $this->timetableRepository->removeStartingFrom($data->getStartDate());
    }

    public function onFinished(ImportResult $result): void {
        $request = $result->getRequest();

        if(!$request instanceof TimetableLessonsData) {
            return;
        }
    }
}