<?php

namespace App\Book\Lesson;

use App\Entity\Lesson;
use App\Repository\LessonRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Section\SectionResolver;
use App\Section\SectionResolverInterface;
use App\Settings\TimetableSettings;
use DateTime;
use Psr\Log\LoggerInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Stopwatch\Stopwatch;

class LessonCreator {
    private $timetableLessonRepository;
    private $lessonRepository;
    private $tuitionRepository;
    private $sectionResolver;
    private $timetableSettings;

    private $dateHelper;
    private $logger;

    public function __construct(TimetableLessonRepositoryInterface $timetableLessonRepository, LessonRepositoryInterface $lessonRepository,
                                TuitionRepositoryInterface $tuitionRepository, SectionResolverInterface $sectionResolver, TimetableSettings $timetableSettings,
                                DateHelper $dateHelper, LoggerInterface $logger) {
        $this->timetableLessonRepository = $timetableLessonRepository;
        $this->lessonRepository = $lessonRepository;
        $this->tuitionRepository = $tuitionRepository;
        $this->sectionResolver = $sectionResolver;
        $this->timetableSettings = $timetableSettings;
        $this->dateHelper = $dateHelper;
        $this->logger = $logger;
    }

    public function createLessons(DateTime $start, DateTime $end) {
        $current = clone $start;
        while($current <= $end) {
            $this->createLessonsForDay($current);
            $current = $current->modify('+1 day');
        }
    }

    public function createLessonsForDay(DateTime $dateTime) {
        if($dateTime > $this->dateHelper->getToday()) {
            return;
        }

        $weekNumber = (int)$dateTime->format('W');
        $section = $this->sectionResolver->getSectionForDate($dateTime);

        if($section === null) {
            $this->logger->notice(sprintf('Cannot create lessons as date "%s" is not part of any section.', $dateTime->format('Y-m-d')));
            return null;
        }

        $existingLessonsCount = $this->lessonRepository->countByDate($dateTime, $dateTime);

        if($existingLessonsCount > 0) {
            return;
        }

        if(in_array((int)$dateTime->format('N') % 7, $this->timetableSettings->getDays()) === false) {
            return;
        }

        $tuitions = $this->tuitionRepository->findAllBySection($section);
        $timetableLessons = $this->timetableLessonRepository->findAllByTuitionsAndWeeks($tuitions, [ $weekNumber ]);

        $this->lessonRepository->beginTransaction();

        foreach($timetableLessons as $timetableLesson) {
            if($dateTime < $timetableLesson->getPeriod()->getStart() || $dateTime > $timetableLesson->getPeriod()->getEnd()) {
                continue;
            }

            if((int)$dateTime->format('N') !== $timetableLesson->getDay()) {
                continue;
            }

            $lesson = (new Lesson())
                ->setTuition($timetableLesson->getTuition())
                ->setDate(clone $dateTime)
                ->setLessonStart($timetableLesson->getLesson())
                ->setLessonEnd($timetableLesson->getLesson() + ($timetableLesson->isDoubleLesson() ? 1 : 0));

            $this->lessonRepository->persist($lesson);
        }

        $this->lessonRepository->commit();
    }
}