<?php

namespace App\Command;

use App\Exam\ExamStudentsResolver;
use App\Repository\ExamRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:exams:resolve_tuitions', description: 'Für alle Klausurschreibende wird geschaut, in welchem Unterricht sie Klausur schreiben (sofern dies nicht bereits festgelegt ist).')]
readonly class ResolveExamStudentTuitionsCommand {
    public function __construct(private ExamRepositoryInterface $examRepository, private ExamStudentsResolver $resolver) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output): int {
        $exams = $this->examRepository->findAll();

        $progress = $style->createProgressBar(count($exams));

        foreach($exams as $exam) {
            $examStudents = $this->resolver->resolveExamStudentsFromGivenStudents($exam, $exam->getStudents()->toArray());
            $this->resolver->setExamStudents($exam, $examStudents);
            $this->examRepository->persist($exam);

            $progress->advance();
        }

        $style->success('Klausuren erfolgreich aktualisiert');

        return Command::SUCCESS;
    }
}