<?php

namespace App\Untis\Gpu\Substitution;

/**
 * @source https://github.com/stuebersystems/enbrea.untis.gpu/blob/d15cc282041fa2d116fc9f9f2c11ab1f616befb5/src/Entities/GpuRecord.cs#L61
 */
enum SubstitutionType: string {
    case ShiftedTo = 'T';
    case ShiftedFrom = 'F';
    case Exchange = 'W';
    case Supervision = 'S';
    case SpecialOccurence = 'A';
    case Cancellation = 'C';
    case Exemption = 'L';
    case PartialExchange = 'P';
    case RoomExchange = 'R';
    case BreakSupervision = 'B';
    case TeacherExchange = '~';
    case Exam = 'E';
}