<?php

namespace App\Book\Command;

use App\Book\Statistics\GenerateStudentTimetableAttendanceStatisticsMessage;
use App\Framework\Feature\Feature;
use App\Framework\Feature\FeatureManager;
use App\Common\Repository\StudentRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand('app:book:generate:student_attendance_statistics', description: 'Veranlasst das asynchrone Berechnen der Statistik über die Anwesenheit im Kontext von Stundenplanstunden für alle Schülerinnen und Schüler. Berücksichtigt Unterrichte im als aktuell ausgewählten Schuljahresabschnitt.')]
#[AsCronTask('@hourly')]
readonly class RegenerateStudentTimetableAttendanceStatistics {

    public function __construct(
        private SectionResolverInterface $sectionResolver,
        private MessageBusInterface $messageBus,
        private FeatureManager $featureManager,
        private StudentRepositoryInterface $studentRepository
    ) {

    }

    public function __invoke(SymfonyStyle $style): int {
        if(!$this->featureManager->isFeatureEnabled(Feature::Book)) {
            $style->success('Unterrichtsbücher sind deaktiviert - tue nichts.');
            return Command::INVALID;
        }

        $section = $this->sectionResolver->getCurrentSection();

        if($section === null) {
            $style->error('Es gibt aktuell kein Schuljahresabschnitt.');
            return Command::FAILURE;
        }

        $count = 0;
        foreach($this->studentRepository->findAllBySection($section) as $student) {
            $message = new GenerateStudentTimetableAttendanceStatisticsMessage($student->getId(), $section->getStart(), $section->getEnd());
            $this->messageBus->dispatch($message);
            $count++;
        }

        $style->success(sprintf('%d Schülerinnen und Schüler werden berechnet.', $count));

        return Command::SUCCESS;
    }
}