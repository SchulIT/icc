<?php

namespace App\Untis;

use MyCLabs\Enum\Enum;

/**
 *
 * @method static GpuSubstitutionType ShiftedTo()
 * @method static GpuSubstitutionType ShiftedFrom()
 * @method static GpuSubstitutionType Exchange()
 * @method static GpuSubstitutionType Supervision()
 * @method static GpuSubstitutionType SpecialOccurence()
 * @method static GpuSubstitutionType Cancellation()
 * @method static GpuSubstitutionType Exemption()
 * @method static GpuSubstitutionType PartialExchange()
 * @method static GpuSubstitutionType RoomExchange()
 * @method static GpuSubstitutionType BreakSupervision()
 * @method static GpuSubstitutionType TeacherExchange()
 * @method static GpuSubstitutionType Exam()
 *
 * @source https://github.com/stuebersystems/enbrea.untis.gpu/blob/d15cc282041fa2d116fc9f9f2c11ab1f616befb5/src/Entities/GpuRecord.cs#L61
 */
class GpuSubstitutionType extends Enum {
    private const ShiftedTo = 'T';
    private const ShiftedFrom = 'F';
    private const Exchange = 'W';
    private const Supervision = 'S';
    private const SpecialOccurence = 'A';
    private const Cancellation = 'C';
    private const Exemption = 'L';
    private const PartialExchange = 'P';
    private const RoomExchange = 'R';
    private const BreakSupervision = 'B';
    private const TeacherExchange = '~';
    private const Exam = 'E';
}