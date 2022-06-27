<?php

namespace App\Tests\Untis\Html;

use App\Repository\SettingRepositoryInterface;
use App\Settings\SettingsManager;
use App\Settings\UntisHtmlSettings;
use App\Untis\Html\Substitution\AbsentRoomsInfotextReader;
use App\Untis\Html\Substitution\AbsentStudyGroupsInfotextReader;
use App\Untis\Html\Substitution\AbsentTeachersInfotextReader;
use App\Untis\Html\Substitution\FreeLessonsInfotextReader;
use App\Untis\Html\Substitution\InfotextReader;
use App\Untis\Html\Substitution\SubstitutionReader;
use App\Untis\Html\Substitution\TableCellParser;
use DateTime;
use PHPUnit\Framework\TestCase;

class HtmlSubstitutionReaderTest extends TestCase {
    private function loadHtml(string $filename): string {
        return file_get_contents(realpath(__DIR__ . '/' .$filename));
    }

    public function testCorrectValues() {
        $html = $this->loadHtml('test.htm');
        $repository = $this->createMock(SettingRepositoryInterface::class);
        $repository->method('findAll')->willReturn([]);

        $reader = new SubstitutionReader([
            new AbsentRoomsInfotextReader(),
            new AbsentStudyGroupsInfotextReader(),
            new AbsentTeachersInfotextReader(),
            new FreeLessonsInfotextReader(),
            new InfotextReader()
        ], new TableCellParser(), new UntisHtmlSettings(new SettingsManager($repository)));
        libxml_use_internal_errors(true) AND libxml_clear_errors();
        $result = $reader->readHtml($html);

        $this->assertEquals(new DateTime('2019-06-10'), $result->getDateTime());
        $this->assertEquals(3, count($result->getInfotexts()));
        $this->assertEquals(2, count($result->getFreeLessons()));
        $this->assertEquals(8, count($result->getAbsences()));

        $this->assertEquals(7, count($result->getSubstitutions()));

        $firstSubstitution = $result->getSubstitutions()[0];
        $this->assertEquals(1, $firstSubstitution->getId());
        $this->assertEquals(1, $firstSubstitution->getLessonStart());
        $this->assertEquals(1, $firstSubstitution->getLessonEnd());
        $this->assertTrue($firstSubstitution->isSupervision());
        $this->assertEquals([], $firstSubstitution->getGrades());
        $this->assertEquals([], $firstSubstitution->getReplacementGrades());
        $this->assertEquals(['XXXX'], $firstSubstitution->getTeachers());
        $this->assertEquals(['YYYY'], $firstSubstitution->getReplacementTeachers());
        $this->assertNull($firstSubstitution->getSubject());
        $this->assertNull($firstSubstitution->getReplacementSubject());
        $this->assertEquals(['Hof'], $firstSubstitution->getRooms());
        $this->assertEquals(['HofNeu'], $firstSubstitution->getReplacementRooms());
        $this->assertEquals('Pausenaufsicht', $firstSubstitution->getType());
        $this->assertNull($firstSubstitution->getRemark());

        $substitutionWithEmptyValue = $result->getSubstitutions()[5];
        $this->assertEquals(6, $substitutionWithEmptyValue->getId());
        $this->assertEquals([], $substitutionWithEmptyValue->getReplacementTeachers());
        $this->assertNull($substitutionWithEmptyValue->getSubject());
        $this->assertEquals(['08A', '08B', '08C'], $substitutionWithEmptyValue->getGrades());

        $substitutionWithText = $result->getSubstitutions()[1];
        $this->assertEquals(2, $substitutionWithText->getId());
        $this->assertEquals('Lorem ipsum', $substitutionWithText->getRemark());

        $cancelledSubstitution = $result->getSubstitutions()[5];
        $this->assertEquals(6, $cancelledSubstitution->getId());
        $this->assertEquals([], $cancelledSubstitution->getReplacementTeachers());
        $this->assertEquals([], $cancelledSubstitution->getReplacementGrades());
        $this->assertEquals([], $cancelledSubstitution->getReplacementRooms());
        $this->assertNull($cancelledSubstitution->getReplacementSubject());
    }
}