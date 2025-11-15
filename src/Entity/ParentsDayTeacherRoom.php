<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class ParentsDayTeacherRoom {

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ParentsDay::class, inversedBy: 'teacherRooms')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ParentsDay $parentsDay;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private Teacher $teacher;

    #[ORM\ManyToOne(targetEntity: Room::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private Room $room;

    public function getParentsDay(): ParentsDay {
        return $this->parentsDay;
    }

    public function setParentsDay(ParentsDay $parentsDay): ParentsDayTeacherRoom {
        $this->parentsDay = $parentsDay;
        return $this;
    }

    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher): ParentsDayTeacherRoom {
        $this->teacher = $teacher;
        return $this;
    }

    public function getRoom(): Room {
        return $this->room;
    }

    public function setRoom(Room $room): ParentsDayTeacherRoom {
        $this->room = $room;
        return $this;
    }
}