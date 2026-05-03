<?php

namespace App\ParentsDay\Room;

use App\Entity\ParentsDayTeacherRoom;
use Symfony\Component\Validator\Constraints\Valid;

class ParentsDayRoomsRequest {
    /**
     * @var ParentsDayTeacherRoom[]
     */
    #[Valid]
    public array $teacherRooms = [ ];
}