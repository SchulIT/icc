<?php

namespace App\Command;

use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Repository\UserTypeEntityRepositoryInterface;
use App\Utils\ArrayUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

#[AsCommand('app:setup', 'Installiert die Anwendungen')]
readonly class SetupCommand {

    public function __construct(private UserTypeEntityRepositoryInterface $userTypeEntityRepository, private EntityManagerInterface $em, private PdoSessionHandler $pdoSessionHandler) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output): int {
        $this->setupSessions($style);
        $this->addMissingUserTypeEntities($style);
        $this->addMissingWeeks($style);

        return Command::SUCCESS;
    }

    private function addMissingUserTypeEntities(SymfonyStyle $style) {
        /** @var UserType[] $types */
        $types = UserType::cases();
        $existingTypes = ArrayUtils::createArrayWithKeys(
            $this->userTypeEntityRepository->findAll(),
            fn(UserTypeEntity $userType) => $userType->getUserType()->value);

        foreach($types as $type) {
            if(array_key_exists($type->value, $existingTypes)) {
                $style->text(sprintf('%s Benutzertyp existiert bereits', $type->value));
            } else {
                $style->text(sprintf('%s Benutzertyp hinzugefügt', $type->value));
                $this->userTypeEntityRepository->persist((new UserTypeEntity())->setUserType($type));
            }
        }

        $style->success('Alle fehlenden Benutzertypen hinzugefügt');
    }

    private function setupSessions(SymfonyStyle $style) {
        $sql = "SHOW TABLES LIKE 'sessions';";
        $row = $this->em->getConnection()->executeQuery($sql);

        if($row->fetchAssociative() === false) {
            $this->pdoSessionHandler->createTable();
        }

        $style->success('Sessions-Tabelle erstellt');
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
                $style->text(sprintf('KW %d existiert bereits', $week));
                continue;
            }

            $sql = 'INSERT INTO week (number) VALUES (?)';
            $stmt = $this->em->getConnection()->prepare($sql);
            $stmt->bindValue(1, $week);
            $stmt->executeQuery();

            $style->text(sprintf('KW %d hinzugefügt', $week));
        }

        $style->success('Alle fehlenden KW hinzugefügt');
    }
}