<?php

namespace App\Tests\Untis;

use App\Untis\Gpu\Substitution\SubstitutionFlag;
use App\Untis\Gpu\Substitution\SubstitutionReader;
use App\Untis\Gpu\Substitution\SubstitutionType;
use DateTime;
use League\Csv\Reader;
use PHPUnit\Framework\TestCase;

/**
 * Test cases from https://github.com/stuebersystems/enbrea.untis.gpu/blob/main/test/Xunit/TestGpuSubstitution.cs
 * Big thanks to Stueber Systems
 */
class GpuSubstitutionReaderTest extends TestCase {
    public function testFilledLine() {
        $lines = <<<GPU
12;"20190807";2;3;124;"Stü";"Zim";"Ma";;;;"R233";"R232";;"8a";"Krank";;2;"8a"; S;201906191028;" - "
13;"20190807";3;3;269;"Bol";"Kal";"De";;;;"R103";"R103";;"8b";"Krank";;0;"8b";;201908011452;" + ~-"
14;"20190807";1;;3922;"Stü";"Kal";"En";;"De";;"SH";"KL6";;"2. M1~2. M1+S~2. M2";;"Urlaub";2;"2. M1~2. M1+S~2. M2"; S;201908011452;" -"
GPU;
        $reader = new SubstitutionReader();
        $substitutions = $reader->readGpu(Reader::fromString(trim($lines)));

        $this->assertEquals(3, count($substitutions));

        $current = $substitutions[0];
        $this->assertEquals(12, $current->getId());
        $this->assertEquals(new DateTime('2019-08-07'), $current->getDate());
        $this->assertEquals(2, $current->getLesson());
        $this->assertEquals("Ma", $current->getSubject());
        $this->assertNull($current->getReplacementSubject());
        $this->assertEquals("Stü", $current->getTeacher());
        $this->assertEquals("Zim", $current->getReplacementTeacher());
        $this->assertSame(['R233'], $current->getRooms());
        $this->assertSame(['R232'], $current->getReplacementRooms());
        $this->assertSame(['8a'], $current->getGrades());
        $this->assertSame(['8a'], $current->getReplacementGrades());
        $this->assertNotNull($current->getType());
        $this->assertTrue($current->getType() === SubstitutionType::Supervision);
        $this->assertNull($current->getRemark());

        $current = $substitutions[1];
        $this->assertEquals(13, $current->getId());
        $this->assertEquals(new DateTime('2019-08-07'), $current->getDate());
        $this->assertEquals(3, $current->getLesson());
        $this->assertEquals("De", $current->getSubject());
        $this->assertNull($current->getReplacementSubject());
        $this->assertEquals("Bol", $current->getTeacher());
        $this->assertEquals("Kal", $current->getReplacementTeacher());
        $this->assertSame(['R103'], $current->getRooms());
        $this->assertSame(['R103'], $current->getReplacementRooms());
        $this->assertSame(['8b'], $current->getGrades());
        $this->assertSame(['8b'], $current->getReplacementGrades());
        $this->assertNull($current->getType());
        $this->assertNull($current->getRemark());

        $current = $substitutions[2];
        $this->assertEquals(14, $current->getId());
        $this->assertEquals(new DateTime('2019-08-07'), $current->getDate());
        $this->assertEquals(1, $current->getLesson());
        $this->assertEquals("En", $current->getSubject());
        $this->assertEquals("De", $current->getReplacementSubject());
        $this->assertEquals("Stü", $current->getTeacher());
        $this->assertEquals("Kal", $current->getReplacementTeacher());
        $this->assertSame(['SH'], $current->getRooms());
        $this->assertSame(['KL6'], $current->getReplacementRooms());
        $this->assertSame(['2. M1', '2. M1+S', '2. M2'], $current->getGrades());
        $this->assertSame(['2. M1', '2. M1+S', '2. M2'], $current->getReplacementGrades());
        $this->assertNotNull($current->getType());
        $this->assertTrue($current->getType() === SubstitutionType::Supervision);
        $this->assertEquals("Urlaub", $current->getRemark());
    }
}