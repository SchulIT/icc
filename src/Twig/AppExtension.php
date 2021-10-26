<?php

namespace App\Twig;

use App\Converter\EnumStringConverter;
use App\Converter\FilesizeStringConverter;
use App\Converter\GradesStringConverter;
use App\Converter\StudentStringConverter;
use App\Converter\StudyGroupsGradeStringConverter;
use App\Converter\StudyGroupStringConverter;
use App\Converter\TeacherStringConverter;
use App\Converter\TimestampDateTimeConverter;
use App\Converter\UserStringConverter;
use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use MyCLabs\Enum\Enum;
use ReflectionClass;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

class AppExtension extends AbstractExtension {

    private $teacherConverter;
    private $studentConverter;
    private $userConverter;
    private $studyGroupConverter;
    private $studyGroupsConverter;
    private $filesizeConverter;
    private $timestampConverter;
    private $enumStringConverter;
    private $gradeStringConverter;

    public function __construct(TeacherStringConverter $teacherConverter, StudentStringConverter $studentConverter,
                                UserStringConverter $userConverter, StudyGroupStringConverter $studyGroupConverter,
                                StudyGroupsGradeStringConverter $studyGroupsConverter, FilesizeStringConverter $filesizeConverter,
                                TimestampDateTimeConverter $timestampConverter, EnumStringConverter $enumStringConverter,
                                GradesStringConverter $gradeStringConverter) {
        $this->teacherConverter = $teacherConverter;
        $this->studentConverter = $studentConverter;
        $this->userConverter = $userConverter;
        $this->studyGroupConverter = $studyGroupConverter;
        $this->studyGroupsConverter = $studyGroupsConverter;
        $this->filesizeConverter = $filesizeConverter;
        $this->timestampConverter = $timestampConverter;
        $this->enumStringConverter = $enumStringConverter;
        $this->gradeStringConverter = $gradeStringConverter;
    }

    public function getFilters() {
        return [
            new TwigFilter('teacher', [ $this, 'teacher' ]),
            new TwigFilter('teachers', [ $this, 'teachers' ]),
            new TwigFilter('student', [ $this, 'student' ]),
            new TwigFilter('user', [ $this, 'user' ]),
            new TwigFilter('studygroup', [$this, 'studyGroup']),
            new TwigFilter('studygroups', [ $this, 'studyGroups' ]),
            new TwigFilter('filesize', [ $this, 'filesize' ]),
            new TwigFilter('todatetime', [ $this, 'toDateTime' ]),
            new TwigFilter('enum', [ $this, 'enum']),
            new TwigFilter('grades', [ $this, 'grades' ])
        ];
    }

    public function getTests() {
        return [
            new TwigTest('instanceof', [ $this, 'isInstanceOf' ])
        ];
    }

    public function teacher(?Teacher $teacher, bool $includeAcronym = false) {
        return $this->teacherConverter->convert($teacher, $includeAcronym);
    }

    /**
     * @param Teacher[]|Collection<Teacher> $teachers
     * @param bool $includeAcronyms
     * @param bool $onlyAcronyms
     * @return string
     */
    public function teachers(iterable $teachers, bool $includeAcronyms = false, bool $onlyAcronyms = false) {
        if($teachers instanceof Collection) {
            $teachers = $teachers->toArray();
        }

        return implode(', ', array_map(function(Teacher $teacher) use ($includeAcronyms, $onlyAcronyms) {
            if($onlyAcronyms === true) {
                return $teacher->getAcronym();
            }

            return $this->teacherConverter->convert($teacher, $includeAcronyms);
        }, $teachers));
    }

    public function student(Student $student) {
        return $this->studentConverter->convert($student);
    }

    public function user(User $user, bool $includeUsername = true) {
        return $this->userConverter->convert($user, $includeUsername);
    }

    public function studyGroup(StudyGroup $group, bool $short = false, bool $includeGrades = false) {
        return $this->studyGroupConverter->convert($group, $short, $includeGrades);
    }

    /**
     * @param StudyGroup[]|ArrayCollection|iterable $studyGroups
     * @param bool $sort
     * @param Grade[]|ArrayCollection|iterable $onlyGrades
     * @return string
     */
    public function studyGroups(iterable $studyGroups, bool $sort = false, iterable $onlyGrades = [ ]) {
        return $this->studyGroupsConverter->convert($studyGroups, $sort, $onlyGrades);
    }

    public function filesize(int $bytes) {
        return $this->filesizeConverter->convert($bytes);
    }

    public function toDateTime(int $timestamp) {
        return $this->timestampConverter->convert($timestamp);
    }

    public function enum(Enum $enum): string {
        return $this->enumStringConverter->convert($enum);
    }

    public function isInstanceOf($object, string $className): bool {
        $reflectionClass = new ReflectionClass($className);
        return $reflectionClass->isInstance($object);
    }

    /**
     * @param Grade[] $grades
     * @return string
     */
    public function grades(iterable $grades): string {
        return $this->gradeStringConverter->convert($grades);
    }
}