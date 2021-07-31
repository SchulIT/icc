<?php

namespace App\Command;

use App\Entity\Gender;
use App\Entity\Teacher;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\String\u;

class AnonymizeDatabaseCommand extends Command {
    private $studentRepository;
    private $teacherRepository;
    private $slugger;
    private $faker;

    public function __construct(StudentRepositoryInterface $studentRepository, TeacherRepositoryInterface $teacherRepository,
                                SluggerInterface $slugger, Generator $faker, string $name = null) {
        parent::__construct($name);

        $this->studentRepository = $studentRepository;
        $this->teacherRepository = $teacherRepository;
        $this->slugger = $slugger;
        $this->faker = $faker;
    }

    public function configure() {
        $this->setName('app:anonymize')
            ->setDescription('Anomyizes all students and teachers.');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $style = new SymfonyStyle($input, $output);

        $style->section('Anomyize teachers');
        $this->teacherRepository->beginTransaction();
        foreach($this->teacherRepository->findAll() as $teacher) {
            $style->writeln('> Anonymize ' . $teacher->getLastname() . ', ' . $teacher->getFirstname());
            $teacher->setFirstname($this->faker->firstName($teacher->getGender()->equals(Gender::Male()) ? 'male' : 'female'));
            $teacher->setLastname($this->faker->lastName);
            $teacher->setAcronym($this->generateAcronym($teacher));
            $teacher->setEmail($this->generateEmail($teacher->getFirstname(), $teacher->getLastname()));

            $this->teacherRepository->persist($teacher);
        }

        $this->teacherRepository->commit();
        $style->success('All teachers anonymized');

        $style->section('Anonymize students');
        $this->studentRepository->beginTransaction();;
        foreach($this->studentRepository->findAll() as $student) {
            $style->writeln(sprintf('> Anonymize %s, %s', $student->getLastname(), $student->getFirstname()));
            $student->setFirstname($this->faker->firstName($student->getGender()->equals(Gender::Male()) ? 'male' : 'female'));
            $student->setLastname($this->faker->lastName);
            $student->setEmail($this->generateEmail($student->getFirstname(), $student->getLastname()));

            $this->studentRepository->persist($student);
        }
        $this->studentRepository->commit();

        return 0;
    }

    private function generateAcronym(Teacher $teacher): string {
        $firstname = $this->slugger->slug($teacher->getFirstname());
        $lastname = $this->slugger->slug($teacher->getLastname());

        return sprintf('%s%s', u($lastname)->upper()->truncate('2'), u($firstname)->upper()->truncate(2));
    }

    private function generateEmail(string $firstname, string $lastname): string {
        $firstname = $this->slugger->slug($firstname);
        $lastname = $this->slugger->slug($lastname);

        return sprintf('%s.%s@schulit.dev', u($firstname)->normalize()->lower(), u($lastname)->lower());
    }

}