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
class ResolveExamStudentTuitionsCommand extends Command {
    public function __construct(private readonly ExamRepositoryInterface $examRepository, private readonly ExamStudentsResolver $resolver, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

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