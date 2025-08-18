<?php

namespace App\Command;

use App\Book\EntryOverviewHelper;
use App\Book\Lesson\LessonCancelHelper;
use App\Entity\TimetableLesson;
use App\Repository\TimetableLessonRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('@daily')]
#[AsCommand(name: 'app:book:auto_cancel', description: 'Markiert alle Stunden an einem Tag als Entfall, wenn es einen Entfallgrund gibt.')]
readonly class CancelLessonsOnFreeDaysCommand {

    public function __construct(private DateHelper $dateHelper, private LessonCancelHelper $cancelHelper,
                                private TimetableLessonRepositoryInterface $lessonRepository, private EntryOverviewHelper $overviewHelper) { }

    public function __invoke(SymfonyStyle $io, OutputInterface $output): int {
        $today = $this->dateHelper->getToday();

        $io->section(sprintf('Betrachte %s', $today->format('d.m.Y')));

        $freeTimespans = $this->overviewHelper->computeFreeTimespans($today, $today);
        $io->text(sprintf('Anzahl der freien Zeitbereiche: %d', count($freeTimespans)));

        $lessons = $this->lessonRepository->findAllByDate($today);
        $io->text(sprintf('Anzahl der Unterrichtsstunden am Tag: %d', count($lessons)));

        foreach($freeTimespans as $timespan) {
            $io->section(sprintf('Markiere %d.-%d. Stunde als Entfall: %s', $timespan->getLessonStart(), $timespan->getLessonEnd(), $timespan->getReason()));

            /** @var TimetableLesson[] $affectedLessons */
            $affectedLessons = array_filter($lessons, fn(TimetableLesson $lesson) => $lesson->getTuition() !== null && $lesson->getLessonStart() >= $timespan->getLessonStart() && $timespan->getLessonEnd() >= $lesson->getLessonEnd());

            $io->text(sprintf('Betroffene Stunden: %d', count($affectedLessons)));
            $count = 0;

            foreach($affectedLessons as $affectedLesson) {
                if($affectedLesson->getEntries()->count() === 0) {
                    $this->cancelHelper->cancelLesson($affectedLesson, $timespan->getReason());
                    $count++;
                }
            }

            $io->success(sprintf('%d Stunde(n) als Entfall markiert', $count));
        }

        return Command::SUCCESS;
    }
}