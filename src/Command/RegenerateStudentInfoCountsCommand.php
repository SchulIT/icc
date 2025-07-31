<?php

namespace App\Command;

use App\Book\Student\Cache\CacheWarmupHelper;
use App\Feature\Feature;
use App\Feature\FeatureManager;
use App\Repository\GradeRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Section\SectionResolverInterface;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

#[AsCommand('app:book:generate:student_info', description: 'Veranlasst das asynchrone Berechnen der Lernenden-Übersichten (nur Zahlen) für die entsprechenden Übersichten.')]
#[AsCronJob('*/20 * * * *')]
readonly class RegenerateStudentInfoCountsCommand {
    public function __construct(private GradeRepositoryInterface $gradeRepository,
                                private TeacherRepositoryInterface $teacherRepository,
                                private SectionResolverInterface $sectionResolver,
                                private CacheWarmupHelper $warmupHelper,
                                private FeatureManager $featureManager) {

    }

    /**
     * @throws ExceptionInterface
     */
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

        $style->section('Klassen');

        // GRADES
        foreach($this->gradeRepository->findAll() as $grade) {
            $style->writeln($grade->getName());
            $count += $this->warmupHelper->warmupGrade($grade, $section);
        }

        $style->section('Lehrkräfte');

        // TEACHERS
        foreach($this->teacherRepository->findAllBySection($section) as $teacher) {
            $style->writeln($teacher->getAcronym());
            $count += $this->warmupHelper->warmupTeacher($teacher, $section);
        }

        // TUITIONS
        // tuitions overviews are calculated rather quickly, so no need for background cache warm-up
        /*foreach($this->tuitionRepository->findAllBySection($section) as $tuition) {
            $style->info($tuition->getName());
            $count += $this->warmupHelper->warmupTuition($tuition, $section);
        }*/

        $style->success(sprintf('%d Lernendeübersichte werden berechnet.', $count));

        return Command::SUCCESS;
    }
}