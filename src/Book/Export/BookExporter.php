<?php

namespace App\Book\Export;

use App\Book\EntryOverview;
use App\Book\EntryOverviewHelper;
use App\Book\Student\AbsenceExcuseResolver;
use App\Book\Student\LessonAttendance;
use App\Book\Student\StudentInfo;
use App\Book\Student\StudentInfoResolver;
use App\Entity\BookComment as CommentEntity;
use App\Entity\Grade as GradeEntity;
use App\Entity\GradeMembership;
use App\Entity\GradeTeacher;
use App\Entity\LessonAttendance as LessonAttendanceEntity;
use App\Entity\LessonAttendanceType;
use App\Entity\LessonEntry;
use App\Entity\LessonEntry as LessonEntryEntity;
use App\Entity\Section as SectionEntity;
use App\Entity\Student as StudentEntity;
use App\Entity\StudyGroupMembership;
use App\Entity\Teacher as TeacherEntity;
use App\Entity\TimetableLesson;
use App\Entity\Tuition as TuitionEntity;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\Utils\ArrayUtils;
use InvalidArgumentException;
use JMS\Serializer\SerializerInterface;

class BookExporter {

    private EntryOverviewHelper $overviewHelper;
    private StudentInfoResolver $studentInfoResolver;

    private Sorter $sorter;
    private SerializerInterface $serializer;

    public function __construct(EntryOverviewHelper $overviewHelper, StudentInfoResolver $studentInfoResolver, Sorter $sorter, SerializerInterface $serializer) {
        $this->overviewHelper = $overviewHelper;
        $this->studentInfoResolver = $studentInfoResolver;
        $this->sorter = $sorter;
        $this->serializer = $serializer;
    }

    /**
     * @param Book $book
     * @param StudentEntity[] $students
     * @param TuitionEntity|null $tuition
     * @param SectionEntity $section
     * @param EntryOverview $overview
     * @return Book
     */
    private function export(Book $book, array $students, ?TuitionEntity $tuition, SectionEntity $section, EntryOverview $overview): Book {
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

            foreach ($tuition->getStudyGroup()->getGrades() as $grade) {
                $exportGrade = (new Grade())
                    ->setName($grade->getName());

                /** @var GradeTeacher $teacher */
                foreach ($grade->getTeachers() as $teacher) {
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

        $this->sorter->sort($students, StudentStrategy::class);

        $studentInfo = [ ];

        foreach($students as $student) {
            $info = $this->studentInfoResolver->resolveStudentInfo($student, $section, $tuition !== null ? [ $tuition ] : [ ]);
            $studentInfo[$student->getId()] = $info;
            $book->addStudentSummary(
                (new StudentSummary())
                    ->setStudent($this->castStudent($student, $section))
                    ->setLateMinutesCount($info->getLateMinutesCount())
                    ->setAbsentLessonsCount($info->getAbsentLessonsCount())
                    ->setExcuseStatusNotSetLessonCount($info->getNotExcusedOrNotSetLessonsCount())
                    ->setNotExcusedAbsentLessonCount($info->getNotExcusedAbsentLessonsCount())
            );
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

    private function computeAttendance(LessonEntryEntity $entry, Lesson $lesson): void {

    }

    public function exportGrade(GradeEntity $grade, SectionEntity $section): Book {
        $book = (new Book())
            ->setGrades([$this->castGrade($grade, $section)]);

        $students = $grade->getMemberships()->filter(function(GradeMembership $membership) use($section) {
            return $membership->getSection() === $section;
        })->map(function(GradeMembership $membership) {
            return $membership->getStudent();
        })->toArray();
        $overview = $this->overviewHelper->computeOverviewForGrade($grade, $section->getStart(), $section->getEnd());

        return $this->export($book, $students, null, $section, $overview);
    }

    public function exportTuition(TuitionEntity $tuition, SectionEntity $section): Book {
        $book = (new Book())
            ->setTuition($this->castTuition($tuition));

        $students = $tuition->getStudyGroup()->getMemberships()->map(function(StudyGroupMembership $membership) {
            return $membership->getStudent();
        })->toArray();

        $overview = $this->overviewHelper->computeOverviewForTuition($tuition, $section->getStart(), $section->getEnd());

        return $this->export($book, $students, $tuition, $section, $overview);
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
            ->setSubject($subject !== null ? $subject->getAbbreviation() : null)
            ->setTeacher($this->castTeacher($lessonEntity->getTuition()->getTeachers()->first()));

        return $lesson;
    }

    /**
     * @param LessonEntryEntity $entry
     * @param SectionEntity $section
     * @param StudentInfo[] $studentInfo
     * @return Lesson
     */
    private function castEntry(LessonEntryEntity $entry, SectionEntity $section, array $studentInfo): Lesson {
        $subject = $entry->getTuition()->getSubject();

        $lesson = (new Lesson())
            ->setStart($entry->getLessonStart())
            ->setEnd($entry->getLessonEnd())
            ->setSubject($subject !== null ? $subject->getAbbreviation() : null)
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
                ->setStudent($this->castStudent($attendance->getStudent(), $section))
                ->setType($this->castAttendanceType($attendance->getType()));

            // check if lesson is excused
            if($attendance->getType() === LessonAttendanceType::Absent) {
                $exportAttendance->setAbsentLessonCount(
                    ($entry->getLessonEnd() - $attendance->getAbsentLessons()) < $entry->getLessonStart() ? 1 : 0
                );

                // check info
                $info = $studentInfo[$attendance->getStudent()->getId()] ?? null;

                if($info !== null) {
                    /** @var LessonAttendance $possibleAttendance */
                    $possibleAttendance = ArrayUtils::first($info->getAbsentLessonAttendances(), function(LessonAttendance $lessonAttendance) use($entry, $attendance) {
                        return $lessonAttendance->getAttendance()->getId() === $attendance->getId()
                            && $lessonAttendance->getAttendance()->getEntry() === $entry;
                    });

                    if($possibleAttendance !== null) {
                        $exportAttendance->setIsExcused($possibleAttendance->isExcused());
                    }
                }
            }

            $lesson->addAttendance($exportAttendance);
        }

        return $lesson;
    }

    private function castAttendanceType(int $type): string {
        switch($type) {
            case LessonAttendanceType::Absent:
                return 'absent';

            case LessonAttendanceType::Present:
                return 'present';

            case LessonAttendanceType::Late:
                return 'late';
        }

        throw new InvalidArgumentException(sprintf('$type must be either 0, 1 or 2, %d given', $type));
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
                $comment->getStudents()->map(function(StudentEntity $student) use($section) {
                    return $this->castStudent($student, $section);
                })->toArray()
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

    private function castStudent(StudentEntity $student, SectionEntity $section): Student {
        $grade = $student->getGrade($section);

        return (new Student())
            ->setId($student->getExternalId())
            ->setIdentifier($student->getUniqueIdentifier())
            ->setFirstname($student->getFirstname())
            ->setLastname($student->getLastname())
            ->setGrade($grade !== null ? $grade->getName() : null);
    }
}