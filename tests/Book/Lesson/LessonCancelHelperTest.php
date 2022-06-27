<?php

namespace App\Tests\Book\Lesson;

use App\Book\Lesson\LessonCancelHelper;
use App\Entity\Lesson;
use App\Entity\LessonEntry;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\Tuition;
use App\Repository\LessonRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use DateTime;
use PHPUnit\Framework\TestCase;

class LessonCancelHelperTest extends TestCase {

    private function getLesson(): TimetableLesson {
        $tuition = new Tuition();
        $tuition->setSubject(new Subject());
        $tuition->addTeacher(new Teacher());

        return (new TimetableLesson())
            ->setLessonStart(1)
            ->setLessonEnd(2)
            ->setTuition($tuition)
            ->setDate(new DateTime('2021-08-01'));
    }

    private function getRepositoryMock(): TimetableLessonRepositoryInterface {
        return $this->getMockBuilder(TimetableLessonRepositoryInterface::class)
            ->getMock();
    }

    public function testCancelLessonWithoutAnyEntries() {
        $lesson = $this->getLesson();
        $helper = new LessonCancelHelper($this->getRepositoryMock());
        $helper->cancelLesson($lesson, 'Foo');

        $this->assertEquals(1, $lesson->getEntries()->count());
        $this->assertEquals(1, $lesson->getEntries()->first()->getLessonStart());
        $this->assertEquals(2, $lesson->getEntries()->first()->getLessonEnd());
    }

    public function testCancelLessonWithExistingEntriesAndMissingLessons() {
        $lesson = $this->getLesson();
        $entry = (new LessonEntry())
            ->setTuition($lesson->getTuition())
            ->setSubject($lesson->getTuition()->getSubject())
            ->setTeacher($lesson->getTuition()->getTeachers()->first())
            ->setLesson($lesson)
            ->setLessonStart(1)
            ->setLessonEnd(1);
        $lesson->getEntries()->add($entry);

        $helper = new LessonCancelHelper($this->getRepositoryMock());
        $helper->cancelLesson($lesson, 'Foo');

        $this->assertEquals(2, $lesson->getEntries()->count());
        $this->assertEquals(1, $lesson->getEntries()->first()->getLessonStart());
        $this->assertEquals(1, $lesson->getEntries()->first()->getLessonEnd());
        $this->assertEquals(2, $lesson->getEntries()->get(1)->getLessonStart());
        $this->assertEquals(2, $lesson->getEntries()->get(1)->getLessonEnd());
    }

    public function testCancelLessonWithExistingEntriesAndNoMissingLessons() {
        $lesson = $this->getLesson();
        $entry = (new LessonEntry())
            ->setTuition($lesson->getTuition())
            ->setSubject($lesson->getTuition()->getSubject())
            ->setTeacher($lesson->getTuition()->getTeachers()->first())
            ->setLesson($lesson)
            ->setLessonStart(1)
            ->setLessonEnd(2);
        $lesson->getEntries()->add($entry);

        $helper = new LessonCancelHelper($this->getRepositoryMock());
        $helper->cancelLesson($lesson, 'Foo');

        $this->assertEquals(1, $lesson->getEntries()->count());
        $this->assertEquals(1, $lesson->getEntries()->first()->getLessonStart());
        $this->assertEquals(2, $lesson->getEntries()->first()->getLessonEnd());
    }
}