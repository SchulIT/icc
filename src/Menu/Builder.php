<?php

namespace App\Menu;

use App\Entity\MessageScope;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Repository\MessageRepositoryInterface;
use App\Repository\WikiArticleRepositoryInterface;
use App\Security\Voter\ListsVoter;
use App\Security\Voter\WikiVoter;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Builder {
    private $factory;
    private $authorizationChecker;

    private $wikiRepository;
    private $messageRepository;

    private $tokenStorage;
    private $dateHelper;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker,
                                WikiArticleRepositoryInterface $wikiRepository, MessageRepositoryInterface $messageRepository,
                                TokenStorageInterface $tokenStorage, DateHelper $dateHelper) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->wikiRepository = $wikiRepository;
        $this->messageRepository = $messageRepository;
        $this->tokenStorage = $tokenStorage;
        $this->dateHelper = $dateHelper;
    }

    private function plansMenu(ItemInterface $menu): ItemInterface {
        $plans = $menu->addChild('plans.label', [
            'attributes' => [
                'class' => 'header'
            ]
        ]);

        $menu->addChild('plans.timetable.label', [
            'route' => 'timetable'
        ]);

        $menu->addChild('plans.substitutions.label', [
            'route' => 'substitutions'
        ]);

        $menu->addChild('plans.exams.label', [
            'route' => 'exams'
        ]);

        $menu->addChild('plans.appointments.label', [
            'route' => 'appointments'
        ]);

        $menu->addChild('plans.rooms.label', [
            'route' => 'rooms'
        ]);

        return $plans;
    }

    private function listsMenu(ItemInterface $menu): ItemInterface {
        $lists = $menu->addChild('lists.label', [
            'attributes' => [
                'class' => 'header'
            ]
        ]);

        if($this->authorizationChecker->isGranted(ListsVoter::Tuitions)) {
            $menu->addChild('lists.tuitions.label', [
                'route' => 'list_tuitions'
            ]);
        }

        if($this->authorizationChecker->isGranted(ListsVoter::StudyGroups)) {
            $menu->addChild('lists.study_groups.label', [
                'route' => 'list_studygroups'
            ]);
        }

        if($this->authorizationChecker->isGranted(ListsVoter::Teachers)) {
            $menu->addChild('lists.teachers.label', [
                'route' => 'list_teachers'
            ]);
        }

        return $lists;
    }

    private function wikiMenu(ItemInterface $menu): ItemInterface {
        $wiki = $menu->addChild('wiki.label', [
            'attributes' => [
                'class' => 'header'
            ]
        ]);

        foreach($this->wikiRepository->findAll() as $article) {
            if($this->authorizationChecker->isGranted(WikiVoter::View, $article)) {
                $menu->addChild(sprintf('wiki.%d', $article->getId()), [
                    'label' => $article->getTitle(),
                    'route' => 'show_wiki_article',
                    'routeParameters' => [
                        'id' => $article->getId(),
                        'slug' => $article->getSlug()
                    ]
                ]);
            }
        }

        return $wiki;
    }

    public function adminMenu(array $options): ItemInterface {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'dropdown-menu')
            ->setChildrenAttribute('role', 'menu');

        if($this->authorizationChecker->isGranted('ROLE_DOCUMENTS_ADMIN')) {
            $menu->addChild('admin.documents.label', [
                'route' => 'admin_documents'
            ]);
        }

        if($this->authorizationChecker->isGranted('ROLE_MESSAGE_CREATOR')) {
            $menu->addChild('admin.messages.label', [
                'route' => 'admin_messages'
            ]);
        }

        if($this->authorizationChecker->isGranted('ROLE_APPOINTMENTS_ADMIN')) {
            $menu->addChild('admin.appointments.label', [
                'route' => 'admin_appointments'
            ]);
        }

        if($this->authorizationChecker->isGranted('ROLE_WIKI_ADMIN')) {
            $menu->addChild('admin.wiki.label', [
                'route' => 'admin_wiki'
            ]);
        }

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('admin.teachers.label', [
                'route' => 'admin_teachers'
            ]);
        }

        return $menu;
    }

    public function settingsMenu(array $options): ItemInterface {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'dropdown-menu')
            ->setChildrenAttribute('role', 'menu');

        $menu->addChild('admin.settings.timetable.label', [
            'route' => 'admin_settings_timetable'
        ]);

        $menu->addChild('admin.timetable.weeks.label', [
            'route' => 'admin_timetable_weeks'
        ]);

        $menu->addChild('admin.timetable.periods.label', [
            'route' => 'admin_timetable_periods'
        ]);

        $menu->addChild('admin.settings.exams.label', [
            'route' => 'admin_settings_exams'
        ]);

        return $menu;
    }

    public function mainMenu(array $options) {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'nav nav-pills flex-column');

        $menu->addChild('menu.label', [
            'attributes' => [
                'class' => 'header'
            ]
        ]);

        $menu->addChild('dashboard.label', [
            'route' => 'dashboard'
        ]);

        $menu->addChild('documents.label', [
            'route' => 'documents'
        ]);

        $messageCount = 0;

        /** @var User|null $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if($user instanceof User) {
            $studyGroups = [ ];

            foreach($user->getStudents() as $student) {
                $studyGroups += $student->getStudyGroupMemberships()->map(function (StudyGroupMembership $membership) {
                    return $membership->getStudyGroup();
                })->toArray();
            }

            $messageCount = $this->messageRepository->countBy(MessageScope::Messages(), $user->getUserType(), $this->dateHelper->getToday(), $studyGroups, false);

        }

        $menu->addChild('messages.overview.label', [
            'route' => 'messages'
        ])
            ->setAttribute('count', $messageCount);

        $this->plansMenu($menu);
        $this->listsMenu($menu);
        $this->wikiMenu($menu);

        return $menu;
    }
}