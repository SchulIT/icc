<?php

namespace App\Command;

use App\Entity\AppointmentVisibility;
use App\Entity\DocumentVisibility;
use App\Entity\MessageVisibility;
use App\Entity\TimetablePeriodVisibility;
use App\Entity\UserType;
use App\Entity\WikiArticleVisibility;
use App\Repository\AppointmentVisibilityRepositoryInterface;
use App\Repository\DocumentVisibilityRepositoryInterface;
use App\Repository\MessageVisibilityRepositoryInterface;
use App\Repository\TimetablePeriodVisibilityRepositoryInterface;
use App\Repository\WikiArticleVisibilityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class SetupCommand extends Command {

    private $documentVisibilityRepository;
    private $messageVisibilityRepository;
    private $timetablePeriodVisibilityRepository;
    private $wikiArticleVisibilityRepository;
    private $visibilityRepository;
    private $pdoSessionHandler;

    private $em;

    public function __construct(DocumentVisibilityRepositoryInterface $documentVisibilityRepository, MessageVisibilityRepositoryInterface $messageVisibilityRepository,
                                TimetablePeriodVisibilityRepositoryInterface $timetablePeriodVisibilityRepository, WikiArticleVisibilityRepositoryInterface $wikiArticleVisibilityRepository,
                                AppointmentVisibilityRepositoryInterface $visibilityRepository, EntityManagerInterface $em, PdoSessionHandler $pdoSessionHandler, string $name = null) {
        parent::__construct($name);

        $this->documentVisibilityRepository = $documentVisibilityRepository;
        $this->messageVisibilityRepository = $messageVisibilityRepository;
        $this->timetablePeriodVisibilityRepository = $timetablePeriodVisibilityRepository;
        $this->wikiArticleVisibilityRepository = $wikiArticleVisibilityRepository;
        $this->visibilityRepository = $visibilityRepository;

        $this->em = $em;
        $this->pdoSessionHandler = $pdoSessionHandler;
    }

    public function configure() {
        parent::configure();

        $this
            ->setName('app:setup')
            ->setDescription('Sets up the application.');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $style = new SymfonyStyle($input, $output);

        $this->setupSessions($style);

        $this->addMissingDocumentVisibilities($style);
        $this->addMissingMessageVisibilities($style);
        $this->addMissingWikiVisibilities($style);
        $this->addMissingTimetablePeriodVisibilities($style);
        $this->addMissingAppointmentVisibilities($style);
    }

    private function addMissingVisibility(SymfonyStyle $style, string $type, array $visibilities, \Closure $newVisibilityAction, array $userTypes) {
        $style->section(sprintf('Adding missing %s', $type));

        foreach($userTypes as $value) {
            if(in_array($value, $visibilities)) {
                $style->text(sprintf('%s for user type "%s" already exists', $type, $value));
            } else {
                $newVisibilityAction(new UserType($value));
                $style->text(sprintf('%s for user type "%s" added', $type, $value));
            }
        }

        $style->success(sprintf('Finished adding missing %s', $type));
    }

    private function addMissingDocumentVisibilities(SymfonyStyle $style) {
        $visibilities = array_map(function(DocumentVisibility $documentVisibility) {
                return $documentVisibility->getUserType()->getValue();
            },
            $this->documentVisibilityRepository->findAll()
        );

        $action = function(UserType $userType) {
            $visibility = (new DocumentVisibility())
                ->setUserType($userType);

            $this->documentVisibilityRepository->persist($visibility);
        };

        $this->addMissingVisibility($style, DocumentVisibility::class, $visibilities, $action, UserType::toArray());
    }

    private function addMissingMessageVisibilities(SymfonyStyle $style) {
        $visibilities = array_map(function(MessageVisibility $messageVisibility) {
            return $messageVisibility->getUserType()->getValue();
        },
            $this->messageVisibilityRepository->findAll()
        );

        $action = function(UserType $userType) {
            $visibility = (new MessageVisibility())
                ->setUserType($userType);

            $this->messageVisibilityRepository->persist($visibility);
        };

        $this->addMissingVisibility($style, MessageVisibility::class, $visibilities, $action, UserType::toArray());
    }

    private function addMissingTimetablePeriodVisibilities(SymfonyStyle $style) {
        $visibilities = array_map(function(TimetablePeriodVisibility $timetablePeriodVisibility) {
            return $timetablePeriodVisibility->getUserType()->getValue();
        },
            $this->timetablePeriodVisibilityRepository->findAll()
        );

        $action = function(UserType $userType) {
            $visibility = (new TimetablePeriodVisibility())
                ->setUserType($userType);

            $this->timetablePeriodVisibilityRepository->persist($visibility);
        };

        $this->addMissingVisibility($style, TimetablePeriodVisibility::class, $visibilities, $action, UserType::toArray());
    }

    private function addMissingWikiVisibilities(SymfonyStyle $style) {
        $visibilities = array_map(function(WikiArticleVisibility $wikiArticleVisibility) {
            return $wikiArticleVisibility->getUserType()->getValue();
        },
            $this->wikiArticleVisibilityRepository->findAll()
        );

        $action = function(UserType $userType) {
            $visibility = (new WikiArticleVisibility())
                ->setUserType($userType);

            $this->wikiArticleVisibilityRepository->persist($visibility);
        };

        $this->addMissingVisibility($style, WikiArticleVisibility::class, $visibilities, $action, UserType::toArray());
    }

    private function addMissingAppointmentVisibilities(SymfonyStyle $style) {
        $visibilities = array_map(function(AppointmentVisibility $visibility) {
            return $visibility->getUserType()->getValue();
        },
            $this->visibilityRepository->findAll()
        );

        $action = function(UserType $userType) {
            $visibility = (new AppointmentVisibility())
                ->setUserType($userType);

            $this->visibilityRepository->persist($visibility);
        };

        $types = [
            UserType::Teacher(),
            UserType::Student(),
            UserType::Parent()
        ];

        $this->addMissingVisibility($style, AppointmentVisibility::class, $visibilities, $action, $types);
    }

    private function setupSessions(SymfonyStyle $style) {
        $sql = "SHOW TABLES LIKE 'sessions';";
        $row = $this->em->getConnection()->executeQuery($sql);

        if($row->fetch() === false) {
            $this->pdoSessionHandler->createTable();
        }

        $style->success('Sessions table ready.');
    }
}