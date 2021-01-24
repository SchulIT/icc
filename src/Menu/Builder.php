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
use App\Security\Voter\ResourceReservationVoter;
use App\Security\Voter\RoomVoter;
use App\Security\Voter\SickNoteVoter;
use App\Security\Voter\WikiVoter;
use App\Settings\NotificationSettings;
use App\Settings\SickNoteSettings;
use App\Utils\EnumArrayUtils;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use SchulIT\CommonBundle\DarkMode\DarkModeManagerInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
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
    private $darkModeManager;
    private $notificationSettings;
    private $sickNoteSettings;

    private $idpProfileUrl;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker,
                                WikiArticleRepositoryInterface $wikiRepository, MessageRepositoryInterface $messageRepository,
                                TokenStorageInterface $tokenStorage, DateHelper $dateHelper, UserStringConverter $userConverter,
                                TranslatorInterface $translator, DarkModeManagerInterface $darkModeManager,
                                NotificationSettings $notificationSettings, SickNoteSettings $sickNoteSettings, string $idpProfileUrl) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->wikiRepository = $wikiRepository;
        $this->messageRepository = $messageRepository;
        $this->tokenStorage = $tokenStorage;
        $this->dateHelper = $dateHelper;
        $this->userConverter = $userConverter;
        $this->translator = $translator;
        $this->darkModeManager = $darkModeManager;
        $this->idpProfileUrl = $idpProfileUrl;
        $this->notificationSettings = $notificationSettings;
        $this->sickNoteSettings = $sickNoteSettings;
    }

    private function plansMenu(ItemInterface $menu): ItemInterface {
        $plans = $menu->addChild('plans.label')
            ->setExtra('menu', 'plans')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('icon', 'fa fa-school');

        $plans->addChild('plans.timetable.label', [
            'route' => 'timetable'
        ])
            ->setExtra('icon', 'fa fa-clock');


        $plans->addChild('plans.substitutions.label', [
            'route' => 'substitutions'
        ])
            ->setExtra('icon', 'fas fa-random');

        $plans->addChild('plans.exams.label', [
            'route' => 'exams'
        ])
            ->setExtra('icon', 'fas fa-edit');

        $plans->addChild('plans.appointments.label', [
            'route' => 'appointments'
        ])
            ->setExtra('icon', 'far fa-calendar');

        if($this->authorizationChecker->isGranted(ResourceReservationVoter::View)) {
            $plans->addChild('resources.reservations.label', [
                'route' => 'resource_reservations'
            ])
                ->setExtra('icon', 'fas fa-laptop-house');
        }

        return $plans;
    }

    private function listsMenu(ItemInterface $menu): ItemInterface {
        $lists = $menu->addChild('lists.label')
            ->setExtra('menu', 'lists')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('icon', 'fas fa-list');

        if($this->authorizationChecker->isGranted(ListsVoter::Tuitions)) {
            $lists->addChild('lists.tuitions.label', [
                'route' => 'list_tuitions'
            ])
                ->setExtra('icon', 'fas fa-chalkboard-teacher');
        }


        if($this->authorizationChecker->isGranted(ListsVoter::StudyGroups)) {
            $lists->addChild('lists.study_groups.label', [
                'route' => 'list_studygroups'
            ])
                ->setExtra('icon', 'fas fa-users');
        }

        if($this->authorizationChecker->isGranted(ListsVoter::Teachers)) {
            $lists->addChild('lists.teachers.label', [
                'route' => 'list_teachers'
            ])
                ->setExtra('icon', 'fas fa-sort-alpha-down');
        }

        if($this->authorizationChecker->isGranted(ListsVoter::Privacy)) {
            $lists->addChild('lists.privacy.label', [
                'route' => 'list_privacy'
            ])
                ->setExtra('icon', 'fas fa-user-shield');
        }

        return $lists;
    }

    private function wikiMenu(ItemInterface $menu): ItemInterface {
        $wiki = $menu->addChild('wiki.label', [
            'route' => 'wiki'
        ])
            ->setExtra('menu', 'wiki')
            ->setExtra('icon', 'fab fa-wikipedia-w')
            ->setExtra('menu-container', '#submenu');

        foreach($this->wikiRepository->findAll() as $article) {
            if($this->authorizationChecker->isGranted(WikiVoter::View, $article)) {
                $item = $wiki->addChild(sprintf('wiki.%s', $article->getUuid()), [
                    'label' => $article->getTitle(),
                    'route' => 'show_wiki_article',
                    'routeParameters' => [
                        'uuid' => (string)$article->getUuid(),
                    ]
                ])
                    ->setExtra('icon', !empty($article->getIcon()) ? $article->getIcon() : 'far fa-file');
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

        if(!$user instanceof User) {
            return $menu;
        }

        $displayName = $user->getUsername();

        $userMenu = $menu->addChild('user', [
                'label' => $displayName
            ])
            ->setExtra('icon', 'fa fa-user')
            ->setExtra('menu', 'user')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true);

        $userMenu->addChild('profile.overview.label', [
            'route' => 'profile'
        ])
            ->setExtra('icon', 'far fa-user');

        $userMenu->addChild('profile.label', [
            'uri' => $this->idpProfileUrl
        ])
            ->setLinkAttribute('target', '_blank')
            ->setExtra('icon', 'far fa-address-card');

        $label = 'dark_mode.enable';
        $icon = 'far fa-moon';

        if($this->darkModeManager->isDarkModeEnabled()) {
            $label = 'dark_mode.disable';
            $icon = 'far fa-sun';
        }

        $userMenu->addChild($label, [
            'route' => 'toggle_darkmode'
        ])
            ->setExtra('icon', $icon);

        $menu->addChild('label.logout', [
            'route' => 'logout',
            'label' => ''
        ])
            ->setExtra('icon', 'fas fa-sign-out-alt')
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
            ->setExtra('icon', 'fa fa-cogs')
            ->setAttribute('title', $this->translator->trans('admin.label'))
            ->setExtra('menu', 'admin')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true);

        $settingsMenu = $this->servicesMenu();

        if($settingsMenu->count() > 0) {
            $menu->addChild('admin.settings.label', [
                'route' => 'admin_settings'
            ])
                ->setExtra('icon', 'fas fa-wrench');
        }

        if($this->authorizationChecker->isGranted('ROLE_DOCUMENTS_ADMIN')) {
            $menu->addChild('admin.documents.label', [
                'route' => 'admin_documents'
            ])
                ->setExtra('icon', 'far fa-file-alt');
        }

        if($this->authorizationChecker->isGranted('ROLE_MESSAGE_CREATOR')) {
            $menu->addChild('admin.messages.label', [
                'route' => 'admin_messages'
            ])
                ->setExtra('icon', 'fas fa-envelope-open-text');
        }

        if($this->authorizationChecker->isGranted(ExamVoter::Manage)) {
            $menu->addChild('admin.exams.label', [
                'route' => 'admin_exams'
            ])
                ->setExtra('icon', 'fas fa-pen');
        }

        if($this->authorizationChecker->isGranted('ROLE_APPOINTMENT_CREATOR')) {
            $menu->addChild('admin.appointments.label', [
                'route' => 'admin_appointments'
            ])
                ->setExtra('icon', 'far fa-calendar');
        }

        if($this->authorizationChecker->isGranted('ROLE_WIKI_ADMIN')) {
            $menu->addChild('admin.wiki.label', [
                'route' => 'admin_wiki'
            ])
                ->setExtra('icon', 'fab fa-wikipedia-w');
        }

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('admin.resources.label', [
                'route' => 'admin_resources'
            ])
                ->setExtra('icon', 'fas fa-laptop-house');

            $menu->addChild('admin.timetable.label', [
                'route' => 'admin_timetable'
            ])
                ->setExtra('icon', 'far fa-clock');

            $menu->addChild('admin.subjects.label', [
                'route' => 'admin_subjects'
            ])
                ->setExtra('icon', 'fas fa-graduation-cap');

            $menu->addChild('admin.teachers.label', [
                'route' => 'admin_teachers'
            ])
                ->setExtra('icon', 'fas fa-sort-alpha-down');

            $menu->addChild('admin.displays.label', [
                'route' => 'admin_displays'
            ])
                ->setExtra('icon', 'fas fa-tv');

            $menu->addChild('api.doc', [
                'uri' => '/docs/api/import'
            ])
                ->setExtra('icon', 'fas fa-code');
        }

        return $root;
    }

    public function systemMenu(array $options): ItemInterface {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $menu = $root->addChild('system', [
            'label' => ''
        ])
            ->setExtra('icon', 'fa fa-tools')
            ->setExtra('menu', 'system')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true)
            ->setAttribute('title', $this->translator->trans('system.label'));

        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild('cron.label', [
                'route' => 'admin_cronjobs'
            ])
                ->setExtra('icon', 'fas fa-history');

            $menu->addChild('logs.label', [
                'route' => 'admin_logs'
            ])
                ->setExtra('icon', 'fas fa-clipboard-list');

            $menu->addChild('mails.label', [
                'route' => 'admin_mails'
            ])
                ->setExtra('icon', 'far fa-envelope');

            $menu->addChild('admin.ea.label', [
                'uri' => '/admin/ea'
            ])
                ->setLinkAttribute('target', '_blank')
                ->setExtra('icon', 'fas fa-tools');

            $menu->addChild('audit.label', [
                'uri' => '/admin/audit'
            ])
                ->setLinkAttribute('target', '_blank')
                ->setExtra('icon', 'far fa-eye');
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
            ->setExtra('icon', 'fa fa-wrench')
            ->setExtra('menu', 'settings')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true)
            ->setAttribute('title', $this->translator->trans('admin.settings.label'));

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('admin.settings.dashboard.label', [
                'route' => 'admin_settings_dashboard'
            ]);

            $menu->addChild('admin.settings.notifications.label', [
                'route' => 'admin_settings_notifications'
            ]);

            $menu->addChild('admin.settings.timetable.label', [
                'route' => 'admin_settings_timetable'
            ]);

            $menu->addChild('admin.settings.exams.label', [
                'route' => 'admin_settings_exams'
            ]);

            $menu->addChild('admin.settings.substitutions.label', [
                'route' => 'admin_settings_substitutions'
            ]);

            $menu->addChild('admin.settings.appointments.label', [
                'route' => 'admin_settings_appointments'
            ]);

            $menu->addChild('admin.settings.sick_notes.label', [
                'route' => 'admin_settings_sick_notes'
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
            ->setExtra('icon', 'fa fa-home');

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
            ->setExtra('count', $messageCount)
            ->setExtra('icon', 'fas fa-envelope-open-text');

        $this->plansMenu($menu);
        $this->listsMenu($menu);

        $menu->addChild('documents.label', [
            'route' => 'documents'
        ])
            ->setExtra('icon', 'far fa-file-alt');

        $this->wikiMenu($menu);

        if($this->sickNoteSettings->isEnabled() === true) {
            if($this->authorizationChecker->isGranted(SickNoteVoter::View)) {
                $menu->addChild('sick_notes.label', [
                    'route' => 'sick_notes'
                ])
                    ->setExtra('icon', 'fas fa-clinic-medical');
            } else if($this->authorizationChecker->isGranted(SickNoteVoter::New)) {
                $menu->addChild('sick_notes.add.label', [
                    'route' => 'sick_note'
                ])
                    ->setExtra('icon', 'fas fa-clinic-medical');
            }
        }

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
                ->setExtra('icon', 'fa fa-th')
                ->setExtra('menu', 'services')
                ->setExtra('pull-right', true)
                ->setAttribute('title', $this->translator->trans('services.label'));

            foreach($token->getAttribute('services') as $service) {
                $item = $menu->addChild($service->name, [
                    'uri' => $service->url
                ])
                    ->setAttribute('title', $service->description)
                    ->setLinkAttribute('target', '_blank');

                if(isset($service->icon) && !empty($service->icon)) {
                    $item->setExtra('icon', $service->icon);
                }
            }
        }

        return $root;
    }

    public function notificationsMenu(): ItemInterface {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $enabledFor = $this->notificationSettings->getPushEnabledUserTypes();

        $token = $this->tokenStorage->getToken();

        if($token === null) {
            return $root;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return $root;
        }

        if(EnumArrayUtils::inArray($user->getUserType(), $enabledFor)) {
            $menu = $root->addChild('services', [
                'label' => '',
                'uri' => '#'
            ])
                ->setExtra('icon', 'far fa-bell')
                ->setExtra('pull-right', true)
                ->setAttribute('title', $this->translator->trans('webpush.label'))
                ->setAttribute('data-toggle', 'webpush_modal');
        }

        return $root;
    }
}