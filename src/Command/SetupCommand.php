<?php

namespace App\Command;

use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Repository\UserTypeEntityRepositoryInterface;
use App\Utils\ArrayUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class SetupCommand extends Command {

    protected static $defaultName = 'app:setup';
    public function __construct(private UserTypeEntityRepositoryInterface $userTypeEntityRepository, private EntityManagerInterface $em, private PdoSessionHandler $pdoSessionHandler, string $name = null) {
        parent::__construct($name);
    }

    public function configure() {
        parent::configure();

        $this->setDescription('Sets up the application.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $this->setupSessions($style);
        $this->addMissingUserTypeEntities($style);
        $this->addMissingWeeks($style);

        return 0;
    }

    private function addMissingUserTypeEntities(SymfonyStyle $style) {
        /** @var UserType[] $types */
        $types = UserType::values();
        $existingTypes = ArrayUtils::createArrayWithKeys(
            $this->userTypeEntityRepository->findAll(),
            fn(UserTypeEntity $userType) => $userType->getUserType()->getValue());

        foreach($types as $type) {
            if(array_key_exists($type->getValue(), $existingTypes)) {
                $style->text(sprintf('%s user type already exists', $type->getValue()));
            } else {
                $style->text(sprintf('%s user type added', $type->getValue()));
                $this->userTypeEntityRepository->persist((new UserTypeEntity())->setUserType($type));
            }
        }

        $style->success('Finished adding missing types.');
    }

    private function setupSessions(SymfonyStyle $style) {
        $sql = "SHOW TABLES LIKE 'sessions';";
        $row = $this->em->getConnection()->executeQuery($sql);

        if($row->fetchAssociative() === false) {
            $this->pdoSessionHandler->createTable();
        }

        $style->success('Sessions table ready.');
    }

    private function addMissingWeeks(SymfonyStyle $style) {
        $weeksInDatabase = [ ];

        $sql = "SELECT number FROM week";
        $stmt = $this->em->getConnection()->executeQuery($sql);

        while(($row = $stmt->fetchAssociative()) !== false) {
            $weeksInDatabase[] = intval($row['number']);
        }

        foreach(range(1, 53) as $week) {
            if(in_array($week, $weeksInDatabase)) {
                $style->text(sprintf('Week %d already exists', $week));
                continue;
            }

            $sql = 'INSERT INTO week (number) VALUES (?)';
            $stmt = $this->em->getConnection()->prepare($sql);
            $stmt->bindValue(1, $week);
            $stmt->executeQuery();

            $style->text(sprintf('Week %d added', $week));
        }

        $style->success('Finished adding missing weeks.');
    }
}