<?php

namespace App\Command;

use App\Section\SectionResolverInterface;
use App\TeacherAbsence\TimetableLessonResolver;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:absences:teachers:resolve_lessons')]
class ResolveAbsenceLessonTimetableLessonsCommand extends Command {
    public function __construct(private readonly TimetableLessonResolver $resolver, private readonly SectionResolverInterface $sectionResolver, ?string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        $section = $this->sectionResolver->getCurrentSection();

        if($section === null) {
            $io->error('Es gibt kein aktuelles Schuljahr.');
            return Command::INVALID;
        }

        $io->section(sprintf('Verlinke Stundenplanstunden für den Abschnitt %s (%s bis %s)', $section->getDisplayName(), $section->getStart()->format('m.d.Y'), $section->getEnd()->format('m.d.Y')));
        $this->resolver->resolve($section->getStart(), $section->getEnd());

        $io->success('Fertig');

        return Command::SUCCESS;
    }
}