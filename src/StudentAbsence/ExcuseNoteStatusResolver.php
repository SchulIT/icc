<?php

namespace App\StudentAbsence;

use App\Book\Student\ExcuseCollectionResolver;
use App\Entity\DateLesson;
use App\Entity\StudentAbsence;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Settings\TimetableSettings;

class ExcuseNoteStatusResolver {
    public function __construct(private readonly ExcuseNoteRepositoryInterface $excuseNoteRepository, private readonly TimetableSettings $timetableSettings, private readonly ExcuseCollectionResolver $excuseCollectionResolver) {

    }

    public function getStatus(StudentAbsence $absence): ExcuseNoteStatus {
        if($absence->getType()->isMustApprove()) {
            return new ExcuseNoteStatus([], $absence->isApproved());
        }

        $lessonsToExcuse = $this->expandRangeToDateLessons($absence->getFrom(), $absence->getUntil());
        $collection = $this->excuseCollectionResolver->resolve($this->excuseNoteRepository->findByStudent($absence->getStudent()));

        $isCompletelyExcused = true;
        $items = [ ];

        foreach($lessonsToExcuse as $dateLesson) {
            $key = sprintf('%s-%d', $dateLesson->getDate()->format('Y-m-d'), $dateLesson->getLesson());
            $excuses = $collection[$key] ?? null;

            if($excuses === null || count($excuses) === 0) {
                $isCompletelyExcused = false;
            }

            $items[] = new ExcuseNoteStatusItem($dateLesson, $excuses);
        }

        return new ExcuseNoteStatus($items, $isCompletelyExcused);
    }

    /**
     * @param DateLesson $start
     * @param DateLesson $end
     * @return DateLesson[]
     */
    private function expandRangeToDateLessons(DateLesson $start, DateLesson $end): array {
        $current = $start->clone();
        $dateLessons = [ ];

        while($current->getDate()->format('Y-m-d') !== $end->getDate()->format('Y-m-d') || $current->getLesson() != $end->getLesson()) {
            $dateLessons[] = $current;

            $current = $current->clone();
            if($current->getLesson() === $this->timetableSettings->getMaxLessons()) {
                $current->setLesson(1)->setDate((clone $current->getDate())->modify('+1 day'));
            } else {
                $current->setLesson($current->getLesson() + 1);
            }
        }

        return $dateLessons;
    }
}