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

    private UserTypeEntityRepositoryInterface $userTypeEntityRepository;
    private PdoSessionHandler $pdoSessionHandler;

    private EntityManagerInterface $em;

    public function __construct(UserTypeEntityRepositoryInterface $userTypeEntityRepository, EntityManagerInterface $em, PdoSessionHandler $pdoSessionHandler, string $name = null) {
        parent::__construct($name);

        $this->userTypeEntityRepository = $userTypeEntityRepository;

        $this->em = $em;
        $this->pdoSessionHandler = $pdoSessionHandler;
    }

    public function configure() {
        parent::configure();

        $this
            ->setName('app:setup')
            ->setDescription('Sets up the application.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $this->setupSessions($style);
        $this->addMissingUserTypeEntities($style);

        return 0;
    }

    private function addMissingUserTypeEntities(SymfonyStyle $style) {
        /** @var UserType[] $types */
        $types = UserType::values();
        $existingTypes = ArrayUtils::createArrayWithKeys(
            $this->userTypeEntityRepository->findAll(),
            function(UserTypeEntity $userType) {
                return $userType->getUserType()->getValue();
            });

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
}