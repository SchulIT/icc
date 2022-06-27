<?php

namespace App\Untis\Gpu\Substitution;

use MyCLabs\Enum\Enum;

/**
 *
 * @method static SubstitutionType ShiftedTo()
 * @method static SubstitutionType ShiftedFrom()
 * @method static SubstitutionType Exchange()
 * @method static SubstitutionType Supervision()
 * @method static SubstitutionType SpecialOccurence()
 * @method static SubstitutionType Cancellation()
 * @method static SubstitutionType Exemption()
 * @method static SubstitutionType PartialExchange()
 * @method static SubstitutionType RoomExchange()
 * @method static SubstitutionType BreakSupervision()
 * @method static SubstitutionType TeacherExchange()
 * @method static SubstitutionType Exam()
 *
 * @source https://github.com/stuebersystems/enbrea.untis.gpu/blob/d15cc282041fa2d116fc9f9f2c11ab1f616befb5/src/Entities/GpuRecord.cs#L61
 */
class SubstitutionType extends Enum {
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