<?php

namespace App\Tests\Untis\Html\Timetable;

use App\Untis\Html\Timetable\Lesson;
use App\Untis\Html\Timetable\TimetableReader;
use App\Untis\Html\Timetable\TimetableType;
use PHPUnit\Framework\TestCase;

class TimetableReaderTest extends TestCase {
    public function testBereitschaft() {
        $file = __DIR__ . '/test_Bereit.htm';
        $html = file_get_contents($file);

        $reader = new TimetableReader();
        $result = $reader->readHtml($html, TimetableType::Subject());
        $lessons = $result->getLessons();

        $this->assertEquals('Berei', $result->getObjective());
        $this->assertCount(10, $lessons);

        /** @var Lesson|null $lessonMondayA */
        $lessonMondayA = $this->getFirstOrNull(array_filter($lessons, fn(Lesson $lesson) => $lesson->getDay() === 1 && $lesson->getLessonStart() == 1 && in_array('A', $lesson->getWeeks())));
        $this->assertNotNull($lessonMondayA);
        $this->assertEquals(2, $lessonMondayA->getLessonEnd());
        $this->assertNull($lessonMondayA->getGrade());
        $this->assertEquals(['A'], $lessonMondayA->getWeeks());
        $this->assertEquals('BCDE', $lessonMondayA->getTeacher());

        /** @var Lesson|null $lessonFridayB */
        $lessonFridayB = $this->getFirstOrNull(array_filter($lessons, fn(Lesson $lesson) => $lesson->getDay() === 5 && $lesson->getLessonStart() == 1 && in_array('B', $lesson->getWeeks())));
        $this->assertNotNull($lessonFridayB);
        $this->assertEquals(2, $lessonFridayB->getLessonEnd());
        $this->assertNull($lessonFridayB->getGrade());
        $this->assertEquals(['B'], $lessonFridayB->getWeeks());
        $this->assertEquals('IJKL', $lessonFridayB->getTeacher());
    }

    public function testGrade() {
        $file = __DIR__ . '/test_05A.htm';
        $html = file_get_contents($file);

        $reader = new TimetableReader();
        $result = $reader->readHtml($html, TimetableType::Grade());
        $lessons = $result->getLessons();

        $this->assertEquals('05A', $result->getObjective());
        $this->assertCount(46, $lessons);

        /** @var Lesson|null $lessonMondayAB34 */
        $lessonMondayAB34 = $this->getFirstOrNull(array_filter($lessons, fn(Lesson $lesson) => $lesson->getDay() === 1 && $lesson->getLessonStart() == 3));
        $this->assertNotNull($lessonMondayAB34);
        $this->assertEquals('E', $lessonMondayAB34->getSubject());
        $this->assertEquals('CDEF', $lessonMondayAB34->getTeacher());
        $this->assertEquals('EW003', $lessonMondayAB34->getRoom());
        $this->assertEquals(4, $lessonMondayAB34->getLessonEnd());
        $this->assertEquals('05A', $lessonMondayAB34->getGrade());
        $this->assertEquals(['A', 'B'], $lessonMondayAB34->getWeeks());

        /** @var Lesson|null $lessonMondayAB7 */
        $lessonMondayAB7 = $this->getFirstOrNull(array_filter($lessons, fn(Lesson $lesson) => $lesson->getDay() === 1 && $lesson->getLessonStart() == 7));
        $this->assertEquals('LEZ', $lessonMondayAB7->getSubject());
        $this->assertEquals('CDEF', $lessonMondayAB7->getTeacher());
        $this->assertEquals('EW003', $lessonMondayAB7->getRoom());
        $this->assertEquals(7, $lessonMondayAB7->getLessonEnd());
        $this->assertEquals('05A', $lessonMondayAB7->getGrade());
        $this->assertEquals(['A', 'B'], $lessonMondayAB7->getWeeks());

        /** @var Lesson|null $lessonThursday78 */
        $lessonThursday78 = $this->getFirstOrNull(array_filter($lessons, fn(Lesson $lesson) => $lesson->getDay() === 4 && $lesson->getLessonStart() == 7));
        $this->assertEquals('PK', $lessonThursday78->getSubject());
        $this->assertEquals('BCDE', $lessonThursday78->getTeacher());
        $this->assertEquals('EW003', $lessonThursday78->getRoom());
        $this->assertEquals(8, $lessonThursday78->getLessonEnd());
        $this->assertEquals('05A', $lessonThursday78->getGrade());
        $this->assertEquals(['A', 'B'], $lessonThursday78->getWeeks());

        /** @var Lesson|null $lessonTuesdayA34 */
        $lessonTuesdayA34 = $this->getFirstOrNull(array_filter($lessons, fn(Lesson $lesson) => $lesson->getDay() === 2 && $lesson->getLessonStart() == 3 && in_array('A', $lesson->getWeeks())));
        $this->assertEquals('E', $lessonTuesdayA34->getSubject());
        $this->assertEquals('CDEF', $lessonTuesdayA34->getTeacher());
        $this->assertEquals('EW003', $lessonTuesdayA34->getRoom());
        $this->assertEquals(4, $lessonTuesdayA34->getLessonEnd());
        $this->assertEquals('05A', $lessonTuesdayA34->getGrade());
        $this->assertEquals(['A'], $lessonTuesdayA34->getWeeks());

        /** @var Lesson|null $lessonFridayA8 */
        $lessonFridayA8 = $this->getFirstOrNull(array_filter($lessons, fn(Lesson $lesson) => $lesson->getDay() === 5 && $lesson->getLessonStart() == 8));
        $this->assertEquals('LEZ', $lessonFridayA8->getSubject());
        $this->assertEquals('CDEF', $lessonFridayA8->getTeacher());
        $this->assertEquals('EW003', $lessonFridayA8->getRoom());
        $this->assertEquals(8, $lessonFridayA8->getLessonEnd());
        $this->assertEquals('05A', $lessonFridayA8->getGrade());
        $this->assertEquals(['B', 'A'], $lessonFridayA8->getWeeks());
    }

    private function getFirstOrNull(array $items): mixed {
        if(count($items) > 0) {
            return array_shift($items);
        }

        return null;
    }
}