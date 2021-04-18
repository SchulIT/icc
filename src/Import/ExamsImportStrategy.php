<?php

namespace App\Import;

use App\Entity\Exam;
use App\Entity\ExamSupervision;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Event\ExamImportEvent;
use App\Event\SubstitutionImportEvent;
use App\Repository\ExamRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Request\Data\ExamData;
use App\Request\Data\ExamsData;
use App\Settings\GeneralSettings;
use App\Settings\ImportSettings;
use App\Utils\CollectionUtils;
use App\Utils\ArrayUtils;
use DateTime;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ExamsImportStrategy implements ImportStrategyInterface, PostActionStrategyInterface, InitializeStrategyInterface {

    private $examRepository;
    private $tuitionRepository;
    private $studentRepository;
    private $teacherRepository;
    private $roomRepository;
    private $generalSettings;
    private $importSettings;
    private $dispatcher;

    private $rules = [ ];

    public function __construct(ExamRepositoryInterface $examRepository, TuitionRepositoryInterface $tuitionRepository,
                                StudentRepositoryInterface $studentRepository, TeacherRepositoryInterface $teacherRepository,
                                EventDispatcherInterface $eventDispatcher, RoomRepositoryInterface $roomRepository,
                                GeneralSettings $generalSettings, ImportSettings $importSettings) {
        $this->examRepository = $examRepository;
        $this->tuitionRepository = $tuitionRepository;
        $this->studentRepository = $studentRepository;
        $this->teacherRepository = $teacherRepository;
        $this->roomRepository = $roomRepository;
        $this->generalSettings = $generalSettings;
        $this->importSettings = $importSettings;
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * @param ExamsData $requestData
     * @return Exam[]
     */
    public function getExistingEntities($requestData): array {
        return ArrayUtils::createArrayWithKeys(
            $this->examRepository->findAllExternal(),
            function(Exam $exam) {
                return $exam->getExternalId();
            }
        );
    }

    /**
     * @param ExamData $data
     * @param ExamsData $requestData
     * @return Exam
     */
    public function createNewEntity($data, $requestData) {
        $exam = (new Exam())
            ->setExternalId($data->getId());
        $this->updateEntity($exam, $data, $requestData);

        return $exam;
    }

    /**
     * @param ExamData $object
     * @param Exam[] $existingEntities
     * @return Exam|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param Exam $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param Exam $entity
     * @param ExamsData $requestData
     * @param ExamData $data
     */
    public function updateEntity($entity, $data, $requestData): void {
        $entity->setDate($data->getDate());
        $entity->setDescription($data->getDescription());
        $entity->setLessonStart($data->getLessonStart());
        $entity->setLessonEnd($data->getLessonEnd());

        /*
         * for compatibility reasons only!
         */
        $rooms = $data->getRooms();
        $room = array_shift($rooms);

        if(!empty($room)) {
            $roomEntity = $this->roomRepository->findOneByExternalId($room);

            if($roomEntity === null) {
                throw new ImportException(sprintf('Room "%s" on substitution ID "%s" was not found.', $room, $data->getId()));
            }

            $entity->setRoom($roomEntity);
        } else {
            $entity->setRoom(null);
        }

        // Remove all supervisions which are outside the exam lesson bounds
        $remove = $entity->getSupervisions()->filter(function(ExamSupervision $supervision) use($entity) {
            return $supervision->getLesson() < $entity->getLessonStart() || $supervision->getLesson() > $entity->getLessonEnd();
        });

        foreach($remove as $item) {
            $entity->removeSupervision($item);
        }

        $supervisions = $data->getSupervisions();

        for($lesson = $data->getLessonStart(), $idx = 0; $lesson <= $data->getLessonEnd(); $lesson++, $idx++) {
            $supervision = $entity->getSupervisions()->filter(function(ExamSupervision $supervision) use ($lesson) {
                return $supervision->getLesson() === $lesson;
            })->first();

            if($supervision === false) {
                $supervision = (new ExamSupervision())
                    ->setExam($entity)
                    ->setLesson($lesson);
                $entity->addSupervision($supervision);
            }

            if(!isset($supervisions[$idx])) {
                $entity->removeSupervision($supervision);
                continue;
            }

            $teacher = $this->teacherRepository->findOneByExternalId($supervisions[$idx]);

            if($teacher !== null) {
                $supervision->setTeacher($teacher);
            } else {
                $entity->removeSupervision($supervision);
            }
        }

        if(count($data->getStudents()) > 0) {
            $students = $this->studentRepository->findAllByExternalId($data->getStudents());
        } else {
            $students = $this->resolveStudentsFromRules($entity);
        }

        $students = $this->getStudentsPartIfGiven($students, $data);

        CollectionUtils::synchronize(
            $entity->getStudents(),
            $students,
            function(Student $student) {
                return $student->getId();
            }
        );

        $tuitions = [ ];

        foreach($data->getTuitions() as $tuitionData) {
            $resolvedTuitions = $this->tuitionRepository->findAllByGradeTeacherAndSubjectOrCourse($tuitionData->getGrades(), $tuitionData->getTeachers(), $tuitionData->getSubjectOrCourse());

            if(count($resolvedTuitions) === 0) {
                throw new ImportException(sprintf('Tuition for (%s; %s; %s) on exam ID "%s" was not found.', implode(',', $tuitionData->getGrades()), implode(',', $tuitionData->getTeachers()), $tuitionData->getSubjectOrCourse(), $data->getId()));
            } else if(count($resolvedTuitions) === 1) {
                $tuitions[] = array_shift($resolvedTuitions);
            } else {
                throw new ImportException(sprintf('Tuition for (%s; %s; %s) on exam ID "%s" is ambigious.', implode(',', $tuitionData->getGrades()), implode(',', $tuitionData->getTeachers()), $tuitionData->getSubjectOrCourse(), $data->getId()));
            }
        }

        CollectionUtils::synchronize(
            $entity->getTuitions(),
            $tuitions,
            function(Tuition $tuition) {
                return $tuition->getId();
            }
        );
    }

    /**
     * @param Exam $exam
     * @return Student[]
     */
    private function resolveStudentsFromRules(Exam $exam): array {
        $students = [ ];

        /** @var Tuition $tuition */
        foreach($exam->getTuitions() as $tuition) {
            /** @var StudyGroupMembership $membership */
            foreach($tuition->getStudyGroup()->getMemberships() as $membership) {
                $student = $membership->getStudent();
                $grade = $student->getGrade()->getName();
                if(array_key_exists($grade, $this->rules) && in_array($membership->getType(), $this->rules[$grade])) {
                    $students[$student->getId()] = $student;
                }
            }
        }

        return array_values($students);
    }

    /**
     * @param Exam $entity
     */
    public function persist($entity): void {
        $this->examRepository->persist($entity);
    }

    /**
     * @param Exam $entity
     */
    public function remove($entity): void {
        $this->examRepository->remove($entity);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->examRepository;
    }

    /**
     * @param ExamsData $data
     * @return ExamData[]
     */
    public function getData($data): array {
        return $data->getExams();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return Exam::class;
    }

    public function onFinished(ImportResult $result) {
        /** @var ExamsData $request */
        $request = $result->getRequest();

        if($request->isSuppressNotifications() === false) {
            $this->dispatcher->dispatch(new ExamImportEvent($result->getAdded(), $result->getUpdated(), $result->getRemoved()));
        }
    }

    public function initialize(): void {
        $currentSection = $this->generalSettings->getSection();

        foreach($this->importSettings->getExamRules() as $rule) {
            $grades = array_map('trim', explode(',', $rule['grades']));
            $sections = array_map('trim', explode(',', $rule['sections']));
            $types = array_map('trim',  explode(',', $rule['types']));

            foreach($sections as $section) {
                if($section == $currentSection) {
                    foreach($grades as $grade) {
                        $this->rules[$grade] = $types;
                    }
                }
            }
        }
    }

    /**
     * Returns the parts of the students which are set as part given in the exam texts.
     * It detects "SuS:START-END" and returns only students with lastnames between START and END.
     * @param Student[] $students
     * @param ExamData $data
     * @return Student[]
     */
    private function getStudentsPartIfGiven(array $students, ExamData $data) {
        $regExp = '~(.*|^)SuS:(\w+)-(\w+)(.*|$)~u';

        if($data->getDescription() === null) {
            return $students;
        }

        if(preg_match($regExp, $data->getDescription(), $matches)) {
            $start = $matches[2];
            $end = $matches[3];

            dump($start);
            dump($end);

            if(mb_strlen($start) > 0 && mb_strlen($end) > 0) {
                return array_filter($students, function(Student $student) use ($start, $end) {
                    return strnatcasecmp($start, $student->getLastname()) <= 0
                        && strnatcasecmp($student->getLastname(), $end) <= 0;
                });
            }
        }

        return $students;
    }
}