<?php

namespace App\Book\Command;

use App\Book\Statistics\GenerateBookLessonCountMessage;
use App\Framework\Feature\Feature;
use App\Framework\Feature\FeatureManager;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand('app:book:generate:lesson_count', description: 'Veranlasst das asynchrone Berechnen von gehaltenen bzw. fehlenden Stunden pro Unterricht. Berücksichtigt Unterrichte im als aktuell ausgewählten Schuljahresabschnitt.')]
#[AsCronTask('*/30 * * * *')]
readonly class RegenerateBookLessonCountCommand {

    public function __construct(private TuitionRepositoryInterface $tuitionRepository,
                                private SectionResolverInterface   $sectionResolver,
                                private MessageBusInterface        $messageBus,
                                private FeatureManager             $featureManager) { }

    public function __invoke(SymfonyStyle $style): int {
        if(!$this->featureManager->isFeatureEnabled(Feature::Book)) {
            $style->success('Unterrichtsbücher sind deaktiviert - tue nichts.');
            return Command::SUCCESS;
        }

        $section = $this->sectionResolver->getCurrentSection();

        if($section === null) {
            $style->error('Es gibt aktuell kein Schuljahresabschnitt.');
            return Command::FAILURE;
        }

        $count = 0;

        foreach($this->tuitionRepository->findAllBySection($section) as $tuition) {
            $message = new GenerateBookLessonCountMessage($tuition->getId());
            $this->messageBus->dispatch($message);
            $count++;
        }

        $style->success(sprintf('%d Unterrichte werden berechnet.', $count));

        return Command::SUCCESS;
    }
}