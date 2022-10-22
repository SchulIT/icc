<?php

namespace App\Command;

use App\Entity\Gender;
use App\Entity\Teacher;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\String\u;

class AnonymizeDatabaseCommand extends Command {
    protected static $defaultName = 'app:anonymize';
    public function __construct(private StudentRepositoryInterface $studentRepository, private TeacherRepositoryInterface $teacherRepository,
                                private UserRepositoryInterface $userRepository, private SluggerInterface $slugger, private Generator $faker, string $name = null) {
        parent::__construct($name);
    }

    public function configure() {
        $this->setDescription('Anomyizes all students and teachers.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $style->section('Anomyize teachers');
        $this->teacherRepository->beginTransaction();
        foreach($this->teacherRepository->findAll() as $teacher) {
            $teacher->setFirstname($this->faker->firstName($teacher->getGender()->equals(Gender::Male()) ? 'male' : 'female'));
            $teacher->setLastname($this->faker->lastName);
            $teacher->setEmail($this->generateEmail($teacher->getFirstname(), $teacher->getLastname(), 't.schulit.dev'));

            $this->teacherRepository->persist($teacher);
        }

        $this->teacherRepository->commit();
        $style->success('All teachers anonymized');

        $style->section('Anonymize students');
        $this->studentRepository->beginTransaction();;
        foreach($this->studentRepository->findAll() as $student) {
            $student->setFirstname($this->faker->firstName($student->getGender()->equals(Gender::Male()) ? 'male' : 'female'));
            $student->setLastname($this->faker->lastName);
            $student->setEmail($this->generateEmail($student->getFirstname(), $student->getLastname(), 's.schulit.dev'));

            $this->studentRepository->persist($student);
        }
        $this->studentRepository->commit();

        $style->section('Anonymize users');
        $this->userRepository->beginTransaction();
        foreach($this->userRepository->findAll() as $user) {
            $user->setFirstname($this->faker->firstName);
            $user->setLastname($this->faker->lastName);
            $user->setEmail($this->generateEmail($user->getFirstname(), $user->getLastname(), 'u.schulit.dev'));
            $user->setUsername($user->getEmail());

            $this->userRepository->persist($user);
        }
        $this->userRepository->commit();

        return 0;
    }

    private function generateEmail(string $firstname, string $lastname, $domain): string {
        $firstname = $this->slugger->slug($firstname);
        $lastname = $this->slugger->slug($lastname);

        return sprintf('%s.%s@%s', u($firstname)->normalize()->lower(), u($lastname)->lower(), u($domain)->lower());
    }

}