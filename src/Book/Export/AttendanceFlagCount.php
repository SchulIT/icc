<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class AttendanceFlagCount {
    #[Serializer\Type('integer')]
    #[Serializer\SerializedName('count')]
    private int $count = 0;

    #[Serializer\Type(AttendanceFlag::class)]
    #[Serializer\SerializedName('flag')]
    private AttendanceFlag $flag;

    public function getCount(): int {
        return $this->count;
    }

    public function setCount(int $count): AttendanceFlagCount {
        $this->count = $count;
        return $this;
    }

    public function getFlag(): AttendanceFlag {
        return $this->flag;
    }

    public function setFlag(AttendanceFlag $flag): AttendanceFlagCount {
        $this->flag = $flag;
        return $this;
    }
}