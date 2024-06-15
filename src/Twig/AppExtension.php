<?php

namespace App\Twig;

use App\Converter\EnumStringConverter;
use App\Converter\FancyUserStringConverter;
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
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use MyCLabs\Enum\Enum;
use ReflectionClass;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

class AppExtension extends AbstractExtension {

    public function __construct(private readonly TeacherStringConverter $teacherConverter, private readonly StudentStringConverter $studentConverter,
                                private readonly UserStringConverter $userConverter, private readonly StudyGroupStringConverter $studyGroupConverter,
                                private readonly StudyGroupsGradeStringConverter $studyGroupsConverter, private readonly FilesizeStringConverter $filesizeConverter,
                                private readonly TimestampDateTimeConverter $timestampConverter, private readonly EnumStringConverter $enumStringConverter,
                                private readonly GradesStringConverter $gradeStringConverter, private readonly FancyUserStringConverter $fancyUserStringConverter)
    {
    }

    public function getFilters(): array {
        return [
            new TwigFilter('teacher', [ $this, 'teacher' ]),
            new TwigFilter('teachers', [ $this, 'teachers' ]),
            new TwigFilter('student', [ $this, 'student' ]),
            new TwigFilter('user', [ $this, 'user' ]),
            new TwigFilter('fancy_user', [ $this, 'fancyUser']),
            new TwigFilter('studygroup', [$this, 'studyGroup']),
            new TwigFilter('studygroups', [ $this, 'studyGroups' ]),
            new TwigFilter('filesize', [ $this, 'filesize' ]),
            new TwigFilter('todatetime', [ $this, 'toDateTime' ]),
            new TwigFilter('enum', [ $this, 'enum']),
            new TwigFilter('grades', [ $this, 'grades' ])
        ];
    }

    public function getTests(): array {
        return [
            new TwigTest('instanceof', [ $this, 'isInstanceOf' ])
        ];
    }

    public function teacher(?Teacher $teacher, bool $includeAcronym = false): ?string {
        return $this->teacherConverter->convert($teacher, $includeAcronym);
    }

    /**
     * @param Teacher[]|Collection<Teacher> $teachers
     */
    public function teachers(iterable $teachers, bool $includeAcronyms = false, bool $onlyAcronyms = false): string {
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

    public function student(Student $student, bool $includeGrade = false): string {
        return $this->studentConverter->convert($student, $includeGrade);
    }

    public function user(User|null $user, bool $includeUsername = true): string {
        if($user === null) {
            return 'N/A';
        }

        return $this->userConverter->convert($user, $includeUsername);
    }

    public function fancyUser(User|null $user): string {
        if($user === null) {
            return 'N/A';
        }

        return $this->fancyUserStringConverter->convert($user);
    }

    public function studyGroup(StudyGroup $group, bool $short = false, bool $includeGrades = false): string {
        return $this->studyGroupConverter->convert($group, $short, $includeGrades);
    }

    /**
     * @param StudyGroup[]|iterable $studyGroups
     * @param Grade[]|iterable $onlyGrades
     */
    public function studyGroups(iterable $studyGroups, bool $sort = false, iterable $onlyGrades = [ ]): string {
        return $this->studyGroupsConverter->convert($studyGroups, $sort, $onlyGrades);
    }

    public function filesize(int $bytes): string {
        return $this->filesizeConverter->convert($bytes);
    }

    public function toDateTime(int $timestamp): DateTime {
        return $this->timestampConverter->convert($timestamp);
    }

    public function enum($enum): string {
        return $this->enumStringConverter->convert($enum);
    }

    public function isInstanceOf($object, string $className): bool {
        $reflectionClass = new ReflectionClass($className);
        return $reflectionClass->isInstance($object);
    }

    /**
     * @param Grade[] $grades
     */
    public function grades(iterable $grades): string {
        return $this->gradeStringConverter->convert($grades);
    }
}