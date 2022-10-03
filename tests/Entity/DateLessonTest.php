<?php

namespace App\Tests\Entity;

use App\Entity\DateLesson;
use DateTime;
use Doctrine\DBAL\Types\DateTimeType;
use PHPUnit\Framework\TestCase;

class DateLessonTest extends TestCase {
    public function testInBetween() {
        $start = (new DateLesson())
            ->setDate(new DateTime('2022-03-10 00:00:00'))
            ->setLesson(3);
        $end = (new DateLesson())
            ->setDate(new DateTime('2022-03-12 00:00:00'))
            ->setLesson(6);

        $this->assertFalse(
            (new DateLesson())->setDate(new DateTime('2022-03-10 00:00:00'))->setLesson(2)->isBetween($start, $end)
        );

        $this->assertTrue(
            (new DateLesson())->setDate(new DateTime('2022-03-10 00:00:00'))->setLesson(3)->isBetween($start, $end)
        );

        $this->assertTrue(
            (new DateLesson())->setDate(new DateTime('2022-03-10 00:00:00'))->setLesson(4)->isBetween($start, $end)
        );

        $this->assertTrue(
            (new DateLesson())->setDate(new DateTime('2022-03-11 00:00:00'))->setLesson(1)->isBetween($start, $end)
        );

        $this->assertTrue(
            (new DateLesson())->setDate(new DateTime('2022-03-11 00:00:00'))->setLesson(3)->isBetween($start, $end)
        );

        $this->assertTrue(
            (new DateLesson())->setDate(new DateTime('2022-03-11 00:00:00'))->setLesson(6)->isBetween($start, $end)
        );

        $this->assertTrue(
            (new DateLesson())->setDate(new DateTime('2022-03-11 00:00:00'))->setLesson(7)->isBetween($start, $end)
        );

        $this->assertTrue(
            (new DateLesson())->setDate(new DateTime('2022-03-12 00:00:00'))->setLesson(1)->isBetween($start, $end)
        );

        $this->assertTrue(
            (new DateLesson())->setDate(new DateTime('2022-03-12 00:00:00'))->setLesson(5)->isBetween($start, $end)
        );

        $this->assertTrue(
            (new DateLesson())->setDate(new DateTime('2022-03-12 00:00:00'))->setLesson(6)->isBetween($start, $end)
        );

        $this->assertFalse(
            (new DateLesson())->setDate(new DateTime('2022-03-12 00:00:00'))->setLesson(7)->isBetween($start, $end)
        );

        $this->assertFalse(
            (new DateLesson())->setDate(new DateTime('2022-03-13 00:00:00'))->setLesson(1)->isBetween($start, $end)
        );
    }
}