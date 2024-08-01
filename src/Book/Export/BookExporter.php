<?php

namespace App\Book\Export;

use App\Book\EntryOverview;
use App\Book\EntryOverviewHelper;
use App\Book\Grade\GradeOverviewHelper;
use App\Book\Student\AbsenceExcuseResolver;
use App\Book\Student\LessonAttendance;
use App\Book\Student\StudentInfo;
use App\Book\Student\StudentInfoResolver;
use App\Book\StudentsResolver;
use App\Entity\BookComment as CommentEntity;
use App\Entity\Grade as GradeEntity;
use App\Entity\GradeLimitedMembership;
use App\Entity\GradeMembership;
use App\Entity\GradeTeacher;
use App\Entity\Attendance as LessonAttendanceEntity;
use App\Entity\AttendanceType;
use App\Entity\LessonEntry;
use App\Entity\LessonEntry as LessonEntryEntity;
use App\Entity\Section as SectionEntity;
use App\Entity\Student as StudentEntity;
use App\Entity\StudyGroupMembership;
use App\Entity\Teacher as TeacherEntity;
use App\Entity\TimetableLesson;
use App\Entity\Tuition as TuitionEntity;
use App\Repository\BookStudentInformationRepositoryInterface;
use App\Repository\GradeLimitedMembershipRepositoryInterface;
use App\Repository\GradeResponsibilityRepositoryInterface;
use App\Repository\LessonAttendanceFlagRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\Utils\ArrayUtils;
use InvalidArgumentException;
use JMS\Serializer\SerializerInterface;

class BookExporter {

    public function __construct(private readonly EntryOverviewHelper $overviewHelper, private readonly StudentInfoResolver $studentInfoResolver,
                                private readonly Sorter $sorter, private readonly SerializerInterface $serializer, private readonly StudentsResolver $studentsResolver,
                                private readonly GradeOverviewHelper $gradeOverviewHelper, private readonly TuitionRepositoryInterface $tuitionRepository,
                                private readonly LessonAttendanceFlagRepositoryInterface $flagRepository,
                                private readonly GradeResponsibilityRepositoryInterface $responsibilityRepository,
                                private readonly GradeLimitedMembershipRepositoryInterface $limitedMembershipRepository,
                                private readonly BookStudentInformationRepositoryInterface $bookStudentInformationRepository)
    {
    }

    /**
     * @param StudentEntity[] $students
     */
    private function export(Book $book, array $students, ?TuitionEntity $tuition, ?GradeEntity $grade, SectionEntity $section, EntryOverview $overview): Book {
        $book
            ->setStart($section->getStart())
            ->setEnd($section->getEnd())
            ->setSection($this->castSection($section));

        if($book->getTuition() !== null && $tuition !== null) {
            /** @var TeacherEntity $teacher */
            foreach ($tuition->getTeachers() as $teacher) {
                $book->getTuition()->addTeacher(
                    (new Teacher())
                        ->setAcronym($teacher->getAcronym())
                        ->setId($teacher->getUuid()->toString())
                        ->setFirstname($teacher->getFirstname())
                        ->setLastname($teacher->getLastname())
                        ->setTitle($teacher->getTitle())
                );
            }
        }

        if($tuition !== null) {
            $grades = [];

            foreach ($tuition->getStudyGroup()->getGrades() as $tuitionGrade) {
                $exportGrade = (new Grade())
                    ->setName($tuitionGrade->getName());

                /** @var GradeTeacher $teacher */
                foreach ($tuitionGrade->getTeachers() as $teacher) {
                    $exportGrade->addTeacher(
                        (new Teacher())
                            ->setAcronym($teacher->getTeacher()->getAcronym())
                            ->setId($teacher->getTeacher()->getUuid()->toString())
                            ->setFirstname($teacher->getTeacher()->getFirstname())
                            ->setLastname($teacher->getTeacher()->getLastname())
                            ->setTitle($teacher->getTeacher()->getTitle())
                    );
                }

                $grades[] = $exportGrade;
            }
            $book->setGrades($grades);
        }

        $gradeOverview = null;
        $tuitions = [ ];

        if($tuition !== null) {
            $tuitions = [ $tuition ];
            $gradeOverview = $this->gradeOverviewHelper->computeOverviewForTuition($tuition);
        } else if($grade !== null) {
            $gradeOverview = $this->gradeOverviewHelper->computeForGrade($grade, $section);
            foreach($gradeOverview->getCategories() as $category) {
                $tuitions[$category->getTuition()->getId()] = $category->getTuition();
            }
        }

        if($gradeOverview !== null) {
            foreach($tuitions as $gradeTuition) {
                $studentGrades = new StudentGrades();
                $studentGrades->setTuition($this->castTuition($gradeTuition));

                foreach ($gradeOverview->getCategories() as $category) {
                    if($category->getTuition() !== $gradeTuition) {
                        continue;
                    }

                    if ($category->getCategory()->isExportable()) {
                        $studentGrades->addCategory(
                            (new TuitionGradeCategory())
                                ->setUuid($category->getCategory()->getUuid()->toString())
                                ->setDisplayName($category->getCategory()->getDisplayName())
                        );
                    }
                }

                foreach ($gradeOverview->getRows() as $row) {
                    foreach ($gradeOverview->getCategories() as $category) {
                        if($category->getTuition() !== $gradeTuition) {
                            continue;
                        }

                        if ($category->getCategory()->isExportable()) {
                            $studentGrades->addGrade((new TuitionGrade())
                                ->setStudent($row->getTuitionOrStudent()->getExternalId())
                                ->setGradeCategory($category->getCategory()->getUuid()->toString())
                                ->setEncryptedGrade($row->getGrade($category->getTuition(), $category->getCategory())?->getEncryptedGrade()));
                        }
                    }
                }

                $book->addStudentGrades($studentGrades);
            }
        }

        $this->sorter->sort($students, StudentStrategy::class);

        $studentInfo = [ ];
        $tuitionsToConsider = $tuition !== null ? [ $tuition ] : $this->tuitionRepository->findAllByGrades([$grade], $section);

        foreach($students as $student) {
            $info = $this->studentInfoResolver->resolveStudentInfo($student, $section, $tuitionsToConsider);
            $studentInfo[$student->getId()] = $info;
            $counts = [ ];

            foreach($info->getAttendanceFlagCounts() as $attendanceFlagCount) {
                $counts[] = (new AttendanceFlagCount())
                    ->setCount($attendanceFlagCount->getCount())
                    ->setFlag(
                        (new AttendanceFlag())
                            ->setUuid($attendanceFlagCount->getFlag()->getUuid()->toString())
                            ->setDescription($attendanceFlagCount->getFlag()->getDescription())
                    );
            }


            $book->addStudentSummary(
                (new StudentSummary())
                    ->setStudent($this->castStudent($student, $section, $tuition))
                    ->setLateMinutesCount($info->getLateMinutesCount())
                    ->setAbsentLessonsCount($info->getAbsentLessonsCount())
                    ->setExcuseStatusNotSetLessonCount($info->getNotExcusedOrNotSetLessonsCount())
                    ->setNotExcusedAbsentLessonCount($info->getNotExcusedAbsentLessonsCount())
                    ->setFlagCounts($counts)
            );

            foreach($this->bookStudentInformationRepository->findByStudents([$student], $section->getStart(), $section->getEnd()) as $info) {
                if($info->isIncludeInGradeBookExport() !== true) {
                    continue;
                }

                $book->addAdditionalStudentInformation(
                    (new AdditionalStudentInformation())
                        ->setStudent($this->castStudent($student, $section, $tuition))
                        ->setFrom($info->getFrom())
                        ->setUntil($info->getUntil())
                        ->setContent($info->getContent())
                );
            }
        }

        foreach($this->flagRepository->findAll() as $flag) { // only add (to overview) flag if necessary
            if($tuition !== null && $flag->getSubjects()->count() > 0 && $flag->getSubjects()->contains($tuition->getSubject()) === false) {
                continue;
            }

            $book->addFlag((new AttendanceFlag())->setUuid($flag->getUuid()->toString())->setDescription($flag->getDescription()));
        }

        $weeks = [ ];
        foreach($overview->getDays() as $day) {
            $weekNumber = (int)$day->getDate()->format('W');

            if(!isset($weeks[$weekNumber])) {
                $weeks[$weekNumber] = (new Week())
                    ->setStart(clone $day->getDate())
                    ->setEnd((clone $day->getDate())->modify('+6 days'))
                    ->setWeekNumber($weekNumber);
            }

            $exportDay = (new Day())
                ->setDate(clone $day->getDate());

            foreach($overview->getComments($day->getDate()) as $comment) {
                $exportDay->addComment($this->castComment($comment, $section));
            }

            $lessons = [ ];

            foreach($day->getLessons() as $lesson) {
                if($lesson->getEntry() === null) {
                    $lessons[] = $this->castLesson($lesson->getLesson(), $lesson->getLessonNumber());
                } else {
                    $lessons[$lesson->getEntry()->getUuid()->toString()] = $this->castEntry($lesson->getEntry(), $section, $studentInfo);
                }
            }

            foreach($lessons as $lesson) {
                $exportDay->addLesson($lesson);
            }

            $weeks[$weekNumber]->addDay($exportDay);
        }

        foreach($weeks as $week) {
            $book->addWeek($week);
        }

        return $book;
    }

    public function exportGrade(GradeEntity $grade, SectionEntity $section): Book {
        $book = (new Book())
            ->setGrades([$this->castGrade($grade, $section)]);

        $students = $grade->getMemberships()->filter(fn(GradeMembership $membership) => $membership->getSection() === $section)->map(fn(GradeMembership $membership) => $membership->getStudent())->toArray();
        $students = array_merge(
            $students,
            array_map(
                fn(GradeLimitedMembership $membership) => $membership->getStudent(),
                $this->limitedMembershipRepository->findAllByGradeAndSection($grade, $section)
            )
        );

        $overview = $this->overviewHelper->computeOverviewForGrade($grade, $section->getStart(), $section->getEnd());

        foreach($this->responsibilityRepository->findAllByGrade($grade, $section) as $responsibility) {
            $book->addResponsibility((new Responsibility())->setTask($responsibility->getTask())->setPerson($responsibility->getPerson()));
        }

        return $this->export($book, $students, null, $grade, $section, $overview);
    }

    public function exportTuition(TuitionEntity $tuition, SectionEntity $section): Book {
        $book = (new Book())
            ->setTuition($this->castTuition($tuition));

        $students = $this->studentsResolver->resolve($tuition, true, true);

        $overview = $this->overviewHelper->computeOverviewForTuition($tuition, $section->getStart(), $section->getEnd());

        return $this->export($book, $students, $tuition, null, $section, $overview);
    }

    public function exportGradeXml(GradeEntity $grade, SectionEntity $section): string {
        return $this->serializer->serialize(
            $this->exportGrade($grade, $section),
            'xml'
        );
    }

    public function exportGradeJson(GradeEntity $grade, SectionEntity $section): string {
        return $this->serializer->serialize(
            $this->exportGrade($grade, $section),
            'json'
        );
    }

    public function exportTuitionXml(TuitionEntity $tuition, SectionEntity $section): string {
        return $this->serializer->serialize(
            $this->exportTuition($tuition, $section),
            'xml'
        );
    }

    public function exportTuitionJson(TuitionEntity $tuition, SectionEntity $section): string {
        return $this->serializer->serialize(
            $this->exportTuition($tuition, $section),
            'json'
        );
    }

    private function castLesson(TimetableLesson $lessonEntity, int $lessonNumber): Lesson {
        $subject = $lessonEntity->getTuition()->getSubject();

        $lesson = (new Lesson())
            ->setIsMissing(true)
            ->setStart($lessonNumber)
            ->setEnd($lessonNumber)
            ->setSubject($subject?->getAbbreviation())
            ->setTeacher($this->castTeacher($lessonEntity->getTuition()->getTeachers()->first()));

        return $lesson;
    }

    /**
     * @param StudentInfo[] $studentInfo
     */
    private function castEntry(LessonEntryEntity $entry, SectionEntity $section, array $studentInfo): Lesson {
        $subject = $entry->getTuition()->getSubject();

        $lesson = (new Lesson())
            ->setStart($entry->getLessonStart())
            ->setEnd($entry->getLessonEnd())
            ->setSubject($subject?->getAbbreviation())
            ->setReplacementSubject($entry->getReplacementSubject())
            ->setTeacher($this->castTeacher($entry->getTeacher()))
            ->setReplacementTeacher($this->castTeacher($entry->getReplacementTeacher()))
            ->setWasCancelled($entry->isCancelled())
            ->setTopic($entry->isCancelled() ? $entry->getCancelReason() : $entry->getTopic())
            ->setComment($entry->getComment())
            ->setExercises($entry->getExercises())
            ->setIsMissing(false);

        /** @var LessonAttendanceEntity $attendance */
        foreach($entry->getAttendances() as $attendance) {
            $exportAttendance = (new Attendance())
                ->setComment($attendance->getComment())
                ->setLateMinutesCount($attendance->getLateMinutes())
                ->setStudent($this->castStudent($attendance->getStudent(), $section, null))
                ->setType($this->castAttendanceType($attendance->getType()))
                ->setLesson($attendance->getLesson());

            $flags = [ ];

            foreach($attendance->getFlags() as $flag) {
                $flags[] = (new AttendanceFlag())
                    ->setUuid($flag->getUuid()->toString())
                    ->setDescription($flag->getDescription());
            }

            $exportAttendance->setFlags($flags);

            // check if lesson is excused
            if($attendance->getType() === AttendanceType::Absent) {
                $exportAttendance->setIsZeroAbsentLesson($attendance->isZeroAbsentLesson());

                // check info
                $info = $studentInfo[$attendance->getStudent()->getId()] ?? null;

                if($info !== null) {
                    /** @var LessonAttendance $possibleAttendance */
                    $possibleAttendance = ArrayUtils::first($info->getAbsentLessonAttendances(), fn(LessonAttendance $lessonAttendance) => $lessonAttendance->getAttendance()->getId() === $attendance->getId()
                        && $lessonAttendance->getAttendance()->getEntry() === $entry);

                    if($possibleAttendance !== null) {
                        $exportAttendance->setIsExcused($possibleAttendance->isExcused());
                    }
                }
            }

            $lesson->addAttendance($exportAttendance);
        }

        return $lesson;
    }

    private function castAttendanceType(AttendanceType $type): string
    {
        return match ($type) {
            AttendanceType::Absent => 'absent',
            AttendanceType::Present => 'present',
            AttendanceType::Late => 'late',
            default => throw new InvalidArgumentException(sprintf('$type must be either 0, 1 or 2, %d given', $type)),
        };
    }

    private function castSection(SectionEntity $section): Section {
        return (new Section())
            ->setName($section->getDisplayName())
            ->setYear($section->getYear())
            ->setNumber($section->getNumber());
    }

    private function castGrade(GradeEntity $gradeEntity, SectionEntity $section): Grade {
        $grade = (new Grade())
            ->setName($gradeEntity->getName());

        /** @var GradeTeacher $gradeTeacher */
        foreach($gradeEntity->getTeachers() as $gradeTeacher) {
            if($gradeTeacher->getSection()->getId() === $section->getId()) {
                $grade->addTeacher($this->castTeacher($gradeTeacher->getTeacher()));
            }
        }

        return $grade;
    }

    private function castComment(CommentEntity $comment, SectionEntity $section): Comment {
        return (new Comment())
            ->setDate($comment->getDate())
            ->setTeacher($this->castTeacher($comment->getTeacher()))
            ->setStudents(
                $comment->getStudents()->map(fn(StudentEntity $student) => $this->castStudent($student, $section, null))->toArray()
            )
            ->setComment($comment->getText());
    }

    private function castTuition(TuitionEntity $tuition): Tuition {
        $subject = $tuition->getSubject();

        return (new Tuition())
            ->setId($tuition->getExternalId())
            ->setName($tuition->getName())
            ->setSubject($subject !== null ? $subject->getAbbreviation() : null);
    }

    private function castTeacher(?TeacherEntity $teacher): ?Teacher {
        if($teacher === null) {
            return null;
        }

        return (new Teacher())
            ->setId($teacher->getExternalId())
            ->setAcronym($teacher->getAcronym())
            ->setFirstname($teacher->getFirstname())
            ->setLastname($teacher->getLastname())
            ->setTitle($teacher->getTitle());
    }

    private function castStudent(StudentEntity $student, SectionEntity $section, TuitionEntity|null $tuition): Student {
        $grade = $student->getGrade($section);
        $membershipType = null;

        if($tuition !== null) {
            /** @var StudyGroupMembership|null $membership */
            $membership = $student->getStudyGroupMemberships()->findFirst(fn(int $_, StudyGroupMembership $membership) => $membership->getStudyGroup()->getId() === $tuition->getStudyGroup()->getId());
            $membershipType = $membership?->getType();
        }

        return (new Student())
            ->setId($student->getExternalId())
            ->setFirstname($student->getFirstname())
            ->setLastname($student->getLastname())
            ->setGrade($grade?->getName())
            ->setMembershipType($membershipType);
    }
}