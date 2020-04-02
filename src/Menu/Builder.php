<?php

namespace App\Menu;

use App\Converter\UserStringConverter;
use App\Entity\MessageScope;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Repository\MessageRepositoryInterface;
use App\Repository\WikiArticleRepositoryInterface;
use App\Security\Voter\ExamVoter;
use App\Security\Voter\ListsVoter;
use App\Security\Voter\RoomVoter;
use App\Security\Voter\WikiVoter;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Builder {
    private $factory;
    private $authorizationChecker;

    private $wikiRepository;
    private $messageRepository;

    private $tokenStorage;
    private $dateHelper;
    private $userConverter;
    private $translator;

    private $idpProfileUrl;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker,
                                WikiArticleRepositoryInterface $wikiRepository, MessageRepositoryInterface $messageRepository,
                                TokenStorageInterface $tokenStorage, DateHelper $dateHelper, UserStringConverter $userConverter,
                                TranslatorInterface $translator, string $idpProfileUrl) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->wikiRepository = $wikiRepository;
        $this->messageRepository = $messageRepository;
        $this->tokenStorage = $tokenStorage;
        $this->dateHelper = $dateHelper;
        $this->userConverter = $userConverter;
        $this->translator = $translator;
        $this->idpProfileUrl = $idpProfileUrl;
    }

    private function plansMenu(ItemInterface $menu): ItemInterface {
        $plans = $menu->addChild('plans.label')
            ->setExtra('menu', 'plans')
            ->setExtra('menu-container', '#submenu')
            ->setAttribute('icon', 'fa fa-school');

        $plans->addChild('plans.timetable.label', [
            'route' => 'timetable'
        ]);

        $plans->addChild('plans.substitutions.label', [
            'route' => 'substitutions'
        ]);

        $plans->addChild('plans.exams.label', [
            'route' => 'exams'
        ]);

        $plans->addChild('plans.appointments.label', [
            'route' => 'appointments'
        ]);

        if($this->authorizationChecker->isGranted(RoomVoter::View)) {
            $plans->addChild('plans.rooms.label', [
                'route' => 'rooms'
            ]);
        }

        return $plans;
    }

    private function listsMenu(ItemInterface $menu): ItemInterface {
        $lists = $menu->addChild('lists.label')
            ->setExtra('menu', 'lists')
            ->setExtra('menu-container', '#submenu')
            ->setAttribute('icon', 'fas fa-list');

        if($this->authorizationChecker->isGranted(ListsVoter::Tuitions)) {
            $lists->addChild('lists.tuitions.label', [
                'route' => 'list_tuitions'
            ]);
        }

        if($this->authorizationChecker->isGranted(ListsVoter::StudyGroups)) {
            $lists->addChild('lists.study_groups.label', [
                'route' => 'list_studygroups'
            ]);
        }

        if($this->authorizationChecker->isGranted(ListsVoter::Teachers)) {
            $lists->addChild('lists.teachers.label', [
                'route' => 'list_teachers'
            ]);
        }

        return $lists;
    }

    private function wikiMenu(ItemInterface $menu): ItemInterface {
        $wiki = $menu->addChild('wiki.label', [
            'route' => 'wiki'
        ])->setExtra('menu', 'wiki')
            ->setAttribute('icon', 'fab fa-wikipedia-w')
            ->setExtra('menu-container', '#submenu');

        foreach($this->wikiRepository->findAll() as $article) {
            if($this->authorizationChecker->isGranted(WikiVoter::View, $article)) {
                $wiki->addChild(sprintf('wiki.%d', $article->getId()), [
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

    public function userMenu(array $options): ItemInterface {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $user = $this->tokenStorage->getToken()->getUser();

        if($user === null || !$user instanceof User) {
            return $menu;
        }

        $displayName = $user->getUsername();

        $userMenu = $menu->addChild('user', [
                'label' => $displayName
            ])
            ->setAttribute('icon', 'fa fa-user')
            ->setExtra('menu', 'user')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true);

        $userMenu->addChild('profile.overview.label', [
            'route' => 'profile'
        ]);

        $userMenu->addChild('profile.label', [
            'uri' => $this->idpProfileUrl
        ])
            ->setAttribute('target', '_blank');

        $menu->addChild('label.logout', [
            'route' => 'logout',
            'label' => ''
        ])
            ->setAttribute('icon', 'fas fa-sign-out-alt')
            ->setAttribute('title', $this->translator->trans('auth.logout'));

        return $menu;
    }

    public function adminMenu(array $options): ItemInterface {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $menu = $root->addChild('admin', [
            'label' => ''
        ])
            ->setAttribute('icon', 'fa fa-cogs')
            ->setAttribute('title', $this->translator->trans('admin.label'))
            ->setExtra('menu', 'admin')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true);

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

        if($this->authorizationChecker->isGranted(ExamVoter::Manage)) {
            $menu->addChild('admin.exams.label', [
                'route' => 'admin_exams'
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
            $menu->addChild('admin.timetable.weeks.label', [
                'route' => 'admin_timetable_weeks'
            ]);

            $menu->addChild('admin.timetable.periods.label', [
                'route' => 'admin_timetable_periods'
            ]);

            $menu->addChild('admin.teachers.label', [
                'route' => 'admin_teachers'
            ]);

            $menu->addChild('api.doc', [
                'uri' => '/docs/api/import'
            ]);
        }

        return $root;
    }

    public function settingsMenu(array $options): ItemInterface {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $menu = $root->addChild('settings', [
            'label' => ''
        ])
            ->setAttribute('icon', 'fa fa-wrench')
            ->setExtra('menu', 'settings')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true)
            ->setAttribute('title', $this->translator->trans('admin.settings.label'));

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('admin.settings.timetable.label', [
                'route' => 'admin_settings_timetable'
            ]);

            $menu->addChild('admin.settings.exams.label', [
                'route' => 'admin_settings_exams'
            ]);

            $menu->addChild('admin.settings.substitutions.label', [
                'route' => 'admin_settings_substitutions'
            ]);
        }

        return $root;
    }

    public function mainMenu(array $options) {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'navbar-nav mr-auto');

        $menu->addChild('dashboard.label', [
            'route' => 'dashboard'
        ])
            ->setAttribute('icon', 'fa fa-home');

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
            ->setAttribute('count', $messageCount)
            ->setAttribute('icon', 'fas fa-envelope-open-text');

        $this->plansMenu($menu);
        $this->listsMenu($menu);

        $menu->addChild('documents.label', [
            'route' => 'documents'
        ])
            ->setAttribute('icon', 'far fa-file-alt');

        $this->wikiMenu($menu);

        return $menu;
    }

    public function servicesMenu(): ItemInterface {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $token = $this->tokenStorage->getToken();

        if($token instanceof SamlSpToken) {
            $menu = $root->addChild('services', [
                'label' => ''
            ])
                ->setAttribute('icon', 'fa fa-th')
                ->setExtra('menu', 'services')
                ->setExtra('pull-right', true)
                ->setAttribute('title', $this->translator->trans('services.label'));

            foreach($token->getAttribute('services') as $service) {
                $menu->addChild($service->name, [
                    'uri' => $service->url
                ])
                    ->setAttribute('title', $service->description)
                    ->setAttribute('target', '_blank');
            }
        }

        return $root;
    }
}