<?php

namespace App\Command;

use App\Entity\Gender;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Faker\Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\String\u;

#[AsCommand('app:anonymize', 'Anatomizes all students and teachers.')]
class AnonymizeDatabaseCommand extends Command {

    public function __construct(private readonly StudentRepositoryInterface $studentRepository, private readonly TeacherRepositoryInterface $teacherRepository,
                                private readonly UserRepositoryInterface    $userRepository, private readonly SluggerInterface $slugger, private readonly Generator $faker, string $name = null) {
        parent::__construct($name);
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