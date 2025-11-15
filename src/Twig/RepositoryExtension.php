<?php

namespace App\Twig;

use App\Entity\Grade;
use App\Entity\ParentsDay;
use App\Entity\Room;
use App\Entity\Section;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Repository\ParentsDayTeacherRoomRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RepositoryExtension extends AbstractExtension {

    public function __construct(
        private readonly StudyGroupRepositoryInterface $studyGroupRepository,
        private readonly ParentsDayTeacherRoomRepositoryInterface $parentsDayTeacherRoomRepository
    ) { }

    public function getFunctions(): array {
        return [
            new TwigFunction('get_study_group_by_grade', [ $this, 'getStudyGroupByGrade' ]),
            new TwigFunction('get_room_for_teacher', [ $this, 'getRoomForTeacher' ]),
        ];
    }

    public function getStudyGroupByGrade(Grade $grade, Section $section): ?StudyGroup {
        return $this->studyGroupRepository->findOneByGrade($grade, $section);
    }

    public function getRoomForTeacher(Teacher $teacher, ParentsDay $parentsDay): ?Room {
        return $this->parentsDayTeacherRoomRepository->findRoomByTeacherAndParentsDay($teacher, $parentsDay);
    }
}