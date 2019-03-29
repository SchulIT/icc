<?php

namespace App\Tests\Event;

use App\Entity\Exam;
use App\Event\ExamImportEvent;
use App\Event\ImportEvent;
use PHPUnit\Framework\TestCase;

class ImportEventTest extends TestCase {
    public function testConstructor() {
        $added = [
            (new Exam())->setExternalId('1'),
            (new Exam())->setExternalId('2')
        ];

        $updated = [
            (new Exam())->setExternalId('4'),
            (new Exam())->setExternalId('5'),
            (new Exam())->setExternalId('6')
        ];

        $removed = [
            (new Exam())->setExternalId('3'),
            (new Exam())->setExternalId('7'),
            (new Exam())->setExternalId('10'),
            (new Exam())->setExternalId('12')
        ];


        $event = new ImportEvent($added, $updated, $removed);
        $this->assertEquals($added, $event->getAdded());
        $this->assertEquals($updated, $event->getUpdated());
        $this->assertEquals($removed, $event->getRemoved());
    }
}