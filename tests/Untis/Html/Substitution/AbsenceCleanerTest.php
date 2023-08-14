<?php

namespace App\Tests\Untis\Html\Substitution;

use App\Settings\TimetableSettings;
use App\Settings\UntisSettings;
use App\Untis\Html\Substitution\Absence;
use App\Untis\Html\Substitution\AbsenceCleaner;
use App\Untis\Html\Substitution\AbsenceObjectiveType;
use App\Untis\Html\Substitution\Substitution;
use App\Untis\Html\Substitution\SubstitutionResult;
use DateTime;
use PHPUnit\Framework\TestCase;

class AbsenceCleanerTest extends TestCase {

    private function getUntisSettings(): UntisSettings {
        $settings = $this->createMock(UntisSettings::class);
        $settings
            ->method('getEventsType')
            ->willReturn('Veranst.');
        $settings
            ->method('isRemoveAbsenceOnEventEnabled')
            ->willReturn(true);

        return $settings;
    }

    private function getTimetableSettings(): TimetableSettings {
        $settings = $this->createMock(TimetableSettings::class);
        $settings
            ->method('getMaxLessons')
            ->willReturn(8);

        return $settings;
    }

    public function testSummarizeAbsences() {
        $result = new SubstitutionResult(new DateTime('2023-01-01'));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 1, 2));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 3, 3));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'NOTTEST', 1, 2));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 4, 6));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Room, 'TEST', 5, 6));

        $cleaner = new AbsenceCleaner($this->getUntisSettings(), $this->getTimetableSettings());
        $absences = $cleaner->getCleanedAbsences($result);

        $this->assertCount(3, $absences);

        $testTeacher = $absences[0];
        $noTestTeacher = $absences[1];
        $testRoom = $absences[2];

        $this->assertEquals('TEST', $testTeacher->getObjective());
        $this->assertEquals(AbsenceObjectiveType::Teacher, $testTeacher->getObjectiveType());
        $this->assertEquals(1, $testTeacher->getLessonStart());
        $this->assertEquals(6, $testTeacher->getLessonEnd());

        $this->assertEquals('NOTTEST', $noTestTeacher->getObjective());
        $this->assertEquals(AbsenceObjectiveType::Teacher, $noTestTeacher->getObjectiveType());
        $this->assertEquals(1, $noTestTeacher->getLessonStart());
        $this->assertEquals(2, $noTestTeacher->getLessonEnd());

        $this->assertEquals('TEST', $testRoom->getObjective());
        $this->assertEquals(AbsenceObjectiveType::Room, $testRoom->getObjectiveType());
        $this->assertEquals(5, $testRoom->getLessonStart());
        $this->assertEquals(6, $testRoom->getLessonEnd());
    }

    public function testSummarizeAbsencesWithAnAbsenceBeingSplitIntoTwoSeparateOnes() {
        $result = new SubstitutionResult(new DateTime('2023-01-01'));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 1, 2));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 3, 3));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 5, 5));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 6, 8));

        $cleaner = new AbsenceCleaner($this->getUntisSettings(), $this->getTimetableSettings());
        $absences = $cleaner->getCleanedAbsences($result);

        $this->assertCount(2, $absences);

        $first = $absences[0];
        $second = $absences[1];

        $this->assertEquals(1, $first->getLessonStart());
        $this->assertEquals(3, $first->getLessonEnd());
        $this->assertEquals(5, $second->getLessonStart());
        $this->assertEquals(8, $second->getLessonEnd());
    }

    public function testSummarizeWithLastAbsenceLessonIsSingleLesson() {
        $result = new SubstitutionResult(new DateTime('2023-01-01'));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 1, 1));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 3, 4));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 6, 6));

        $cleaner = new AbsenceCleaner($this->getUntisSettings(), $this->getTimetableSettings());
        $absences = $cleaner->getCleanedAbsences($result);

        $this->assertCount(3, $absences);

        $first = $absences[0];
        $second = $absences[1];
        $third = $absences[2];

        $this->assertEquals(1, $first->getLessonStart());
        $this->assertEquals(1, $first->getLessonEnd());

        $this->assertEquals(3, $second->getLessonStart());
        $this->assertEquals(4, $second->getLessonEnd());

        $this->assertEquals(6, $third->getLessonStart());
        $this->assertEquals(6, $third->getLessonEnd());
    }

    public function testSummarizeWithEmptyAbsenceLessons() {
        $result = new SubstitutionResult(new DateTime('2023-01-01'));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 1, 2));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', null, null));

        $cleaner = new AbsenceCleaner($this->getUntisSettings(), $this->getTimetableSettings());
        $absences = $cleaner->getCleanedAbsences($result);

        $this->assertCount(1, $absences);

        $first = $absences[0];
        $this->assertEquals(1, $first->getLessonStart());
        $this->assertEquals(8, $first->getLessonEnd());
    }

    public function testRemoveAbsenceOnEvent() {
        $result = new SubstitutionResult(new DateTime('2023-01-01'));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 1, 2));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 3, 3));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 5, 5));
        $result->addAbsence(new Absence(AbsenceObjectiveType::Teacher, 'TEST', 6, 8));

        $result->addSubstitution((new Substitution())->setType('Veranst.')->setTeachers(['TEST', 'ANOTHER'])->setLessonStart(1)->setLessonEnd(1));
        $result->addSubstitution((new Substitution())->setType('Veranst.')->setTeachers(['TEST'])->setLessonStart(6)->setLessonEnd(7));

        $cleaner = new AbsenceCleaner($this->getUntisSettings(), $this->getTimetableSettings());
        $absences = $cleaner->getCleanedAbsences($result);

        $this->assertCount(3, $absences);
        $first = $absences[0];
        $second = $absences[1];
        $third = $absences[2];

        $this->assertEquals(2, $first->getLessonStart());
        $this->assertEquals(3, $first->getLessonEnd());
        $this->assertEquals(5, $second->getLessonStart());
        $this->assertEquals(5, $second->getLessonEnd());
        $this->assertEquals(8, $third->getLessonStart());
        $this->assertEquals(8, $third->getLessonEnd());
    }
}