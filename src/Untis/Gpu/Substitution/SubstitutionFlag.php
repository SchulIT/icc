<?php

namespace App\Untis\Gpu\Substitution;

class SubstitutionFlag {
    public const Cancellation = 0x1;
    public const Supervision = 0x2;
    public const SpecialDuty = 0x4;
    public const ShiftedFrom = 0x8;
    public const Release = 0x10;
    public const PlusAsStandIn = 0x20;
    public const PartialStandIn = 0x40;
    public const ShiftedTo = 0x80;
    public const RoomExchange = 0x10000;
    public const SupervisionExchange = 0x20000;
    public const NoLesson = 0x40000;
    public const DoNotPrintFlag = 0x100000;
    public const NewFlag = 0x200000;
}