<?php

namespace App\Twig;

use App\Converter\MessageScopeStringConverter;
use App\Converter\StudentStringConverter;
use App\Converter\StudyGroupStringConverter;
use App\Converter\TeacherStringConverter;
use App\Converter\UserStringConverter;
use App\Converter\UserTypeStringConverter;
use App\Entity\MessageScope;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension {

    private $teacherConverter;
    private $studentConverter;
    private $userTypeConverter;
    private $userConverter;
    private $messageScopeConverter;
    private $studyGroupConverter;

    public function __construct(TeacherStringConverter $teacherConverter, StudentStringConverter $studentConverter,
                                UserTypeStringConverter $userTypeConverter, UserStringConverter $userConverter,
                                MessageScopeStringConverter $messageScopeConverter, StudyGroupStringConverter $studyGroupConverter) {
        $this->teacherConverter = $teacherConverter;
        $this->studentConverter = $studentConverter;
        $this->userTypeConverter = $userTypeConverter;
        $this->userConverter = $userConverter;
        $this->messageScopeConverter = $messageScopeConverter;
        $this->studyGroupConverter = $studyGroupConverter;
    }

    public function getFilters() {
        return [
            new TwigFilter('teacher', [ $this, 'teacher' ]),
            new TwigFilter('student', [ $this, 'student' ]),
            new TwigFilter('usertype', [ $this, 'userType' ]),
            new TwigFilter('user', [ $this, 'user' ]),
            new TwigFilter('messagescope', [ $this, 'messageScope' ]),
            new TwigFilter('studygroup', [$this, 'studyGroup'])
        ];
    }

    public function teacher(Teacher $teacher, bool $includeAcronym = false) {
        return $this->teacherConverter->convert($teacher, $includeAcronym);
    }

    public function student(Student $student) {
        return $this->studentConverter->convert($student);
    }

    public function userType(UserType $userType) {
        return $this->userTypeConverter->convert($userType);
    }

    public function user(User $user) {
        return $this->userConverter->convert($user);
    }

    public function messageScope(MessageScope $scope) {
        return $this->messageScopeConverter->convert($scope);
    }

    public function studyGroup(StudyGroup $group) {
        return $this->studyGroupConverter->convert($group);
    }
}