<?php

declare(strict_types=1);

namespace App\ParentsDay\Twig;

use App\Common\Entity\Room;
use App\Common\Entity\Teacher;
use App\ParentsDay\Entity\ParentsDay;
use App\ParentsDay\Repository\ParentsDayTeacherRoomRepositoryInterface;
use Twig\Attribute\AsTwigFunction;

readonly class ParentsDayRoomRepositoryExtension {
    public function __construct(
        private ParentsDayTeacherRoomRepositoryInterface $parentsDayTeacherRoomRepository
    ) { }

    #[AsTwigFunction('get_room_for_teacher')]
    public function getRoomForTeacher(Teacher $teacher, ParentsDay $parentsDay): ?Room {
        return $this->parentsDayTeacherRoomRepository->findRoomByTeacherAndParentsDay($teacher, $parentsDay);
    }
}