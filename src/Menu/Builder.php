<?php

namespace App\Menu;

use App\Converter\UserStringConverter;
use App\Entity\MessageScope;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Repository\LessonRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\WikiArticleRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\ExamVoter;
use App\Security\Voter\ListsVoter;
use App\Security\Voter\ResourceReservationVoter;
use App\Security\Voter\SickNoteVoter;
use App\Security\Voter\WikiVoter;
use App\Settings\NotificationSettings;
use App\Settings\SickNoteSettings;
use App\Utils\EnumArrayUtils;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
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
    private $lessonRepository;

    private $tokenStorage;
    private $dateHelper;
    private $userConverter;
    private $translator;
    private $darkModeManager;
    private $notificationSettings;
    private $sickNoteSettings;
    private $sectionResolver;

    private $idpProfileUrl;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker,
                                WikiArticleRepositoryInterface $wikiRepository, MessageRepositoryInterface $messageRepository, LessonRepositoryInterface $lessonRepository,
                                TokenStorageInterface $tokenStorage, DateHelper $dateHelper, UserStringConverter $userConverter,
                                TranslatorInterface $translator, DarkModeManagerInterface $darkModeManager,
                                NotificationSettings $notificationSettings, SickNoteSettings $sickNoteSettings, SectionResolverInterface $sectionResolver, string $idpProfileUrl) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->wikiRepository = $wikiRepository;
        $this->messageRepository = $messageRepository;
        $this->lessonRepository = $lessonRepository;
        $this->tokenStorage = $tokenStorage;
        $this->dateHelper = $dateHelper;
        $this->userConverter = $userConverter;
        $this->translator = $translator;
        $this->darkModeManager = $darkModeManager;
        $this->idpProfileUrl = $idpProfileUrl;
        $this->notificationSettings = $notificationSettings;
        $this->sickNoteSettings = $sickNoteSettings;
        $this->sectionResolver = $sectionResolver;
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
            if($article->isOnline() && $this->authorizationChecker->isGranted(WikiVoter::View, $article)) {
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

    private function bookMenu(ItemInterface $menu): ItemInterface {
        $book = $menu->addChild('book.label', [
            'route' => 'book'
        ])
            ->setExtra('menu', 'book')
            ->setExtra('icon', 'fas fa-book-open')
            ->setExtra('menu-container', '#submenu');

        $book->addChild('book.label', [
            'route' => 'book'
        ])
            ->setExtra('icon', 'fas fa-book-open');

        $missing = $book->addChild('book.missing.label', [
            'route' => 'missing_book_entries'
        ])
            ->setExtra('icon', 'fas fa-times');

        $user = $this->tokenStorage->getToken()->getUser();
        $currentSection = $this->sectionResolver->getCurrentSection();

        if($user !== null && $user instanceof User && $user->getTeacher() !== null && $currentSection !== null) {
            $count = $this->lessonRepository->countMissingByTeacher($user->getTeacher(), $currentSection->getStart(), $this->dateHelper->getToday());
            $missing->setExtra('count', $count);
        }

        $book->addChild('book.students.label', [
            'route' => 'book_students'
        ])
            ->setExtra('icon', 'fas fa-users');

        $book->addChild('book.excuse_note.label', [
            'route' => 'excuse_notes'
        ])
            ->setExtra('icon', 'fas fa-pen-alt');

        return $book;
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
            ->setExtra('icon', 'fas fa-user');

        $userMenu->addChild('profile.label', [
            'uri' => $this->idpProfileUrl
        ])
            ->setLinkAttribute('target', '_blank')
            ->setExtra('icon', 'fas fa-address-card');

        $label = 'dark_mode.enable';
        $icon = 'fas fa-moon';

        if($this->darkModeManager->isDarkModeEnabled()) {
            $label = 'dark_mode.disable';
            $icon = 'fas fa-sun';
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

    public function dataMenu(array $options = []): ItemInterface {
        $root = $this->factory->createItem('root');

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $root->addChild('admin.sections.label', [
                'route' => 'admin_sections'
            ])
                ->setExtra('icon', 'fas fa-sliders-h');
        }

        if($this->authorizationChecker->isGranted('ROLE_DOCUMENTS_ADMIN')) {
            $root->addChild('admin.documents.label', [
                'route' => 'admin_documents'
            ])
                ->setExtra('icon', 'fas fa-file-alt');
        }

        if($this->authorizationChecker->isGranted('ROLE_MESSAGE_CREATOR')) {
            $root->addChild('admin.messages.label', [
                'route' => 'admin_messages'
            ])
                ->setExtra('icon', 'fas fa-envelope-open-text');
        }

        if($this->authorizationChecker->isGranted(ExamVoter::Manage)) {
            $root->addChild('admin.exams.label', [
                'route' => 'admin_exams'
            ])
                ->setExtra('icon', 'fas fa-pen');
        }

        if($this->authorizationChecker->isGranted('ROLE_APPOINTMENT_CREATOR')) {
            $root->addChild('admin.appointments.label', [
                'route' => 'admin_appointments'
            ])
                ->setExtra('icon', 'far fa-calendar');
        }

        if($this->authorizationChecker->isGranted('ROLE_WIKI_ADMIN')) {
            $root->addChild('admin.wiki.label', [
                'route' => 'admin_wiki'
            ])
                ->setExtra('icon', 'fab fa-wikipedia-w');
        }

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $root->addChild('admin.resources.label', [
                'route' => 'admin_resources'
            ])
                ->setExtra('icon', 'fas fa-laptop-house');

            $root->addChild('admin.timetable.label', [
                'route' => 'admin_timetable'
            ])
                ->setExtra('icon', 'far fa-clock');

            $root->addChild('admin.teachers.label', [
                'route' => 'admin_teachers'
            ])
                ->setExtra('icon', 'fas fa-sort-alpha-down');
        }

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $root->addChild('admin.subjects.label', [
                'route' => 'admin_subjects'
            ])
                ->setExtra('icon', 'fas fa-graduation-cap');

            $root->addChild('admin.displays.label', [
                'route' => 'admin_displays'
            ])
                ->setExtra('icon', 'fas fa-tv');

            $root->addChild('admin.apps.label', [
                'route' => 'admin_apps'
            ])
                ->setExtra('icon', 'fas fa-mobile-alt');
        }

        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $root->addChild('admin.ea.label', [
                'uri' => '/admin/ea'
            ])
                ->setLinkAttribute('target', '_blank')
                ->setExtra('icon', 'fas fa-tools');
        }

        return $root;
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

        $settingsMenu = $this->settingsMenu();

        if($settingsMenu->offsetExists('settings') && count($settingsMenu['settings']->getChildren()) > 0) {
            $menu->addChild('admin.settings.label', [
                'route' => 'admin_settings'
            ])
                ->setExtra('icon', 'fas fa-wrench');
        }

        $dataMenu = $this->dataMenu();

        if($dataMenu->count() > 0) {
            $firstKey = array_key_first($dataMenu->getChildren());
            $first = $dataMenu->getChildren()[$firstKey];

            $menu->addChild('admin.label', [
                'uri' => $first->getUri()
            ])
                ->setExtra('icon', 'fas fa-school');
        }

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
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

            $menu->addChild('audit.label', [
                'uri' => '/admin/audit'
            ])
                ->setLinkAttribute('target', '_blank')
                ->setExtra('icon', 'far fa-eye');
        }

        return $root;
    }

    public function importMenu(array $options = [ ]): ItemInterface {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $menu = $root->addChild('import', [
            'label' => ''
        ])
            ->setExtra('icon', 'fas fa-upload')
            ->setExtra('menu', 'import')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true)
            ->setAttribute('title', $this->translator->trans('import.label'));

        if($this->authorizationChecker->isGranted('ROLE_IMPORTER')) {
            $menu->addChild('import.settings.label', [
                'route' => 'import_untis_settings'
            ])
                ->setExtra('icon', 'fas fa-cogs');

            $menu->addChild('import.substitutions.label', [
                'route' => 'import_untis_substitutions'
            ])
                ->setExtra('icon', 'fas fa-random');

            $menu->addChild('import.exams.label', [
                'route' => 'import_untis_exams'
            ])
                ->setExtra('icon', 'fas fa-edit');

            $menu->addChild('import.supervisions.label', [
                'route' => 'import_untis_supervisions'
            ])
                ->setExtra('icon', 'fas fa-eye');
        }

        return $root;
    }

    public function settingsMenu(array $options = [ ]): ItemInterface {
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
            $menu->addChild('admin.settings.general.label', [
                'route' => 'admin_settings_general'
            ]);

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

            $menu->addChild('admin.settings.import.label', [
                'route' => 'admin_settings_import'
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

        $menu->addChild('messages.overview.label', [
            'route' => 'messages'
        ])
            ->setExtra('icon', 'fas fa-envelope-open-text');

        $this->plansMenu($menu);
        $this->listsMenu($menu);

        $menu->addChild('documents.label', [
            'route' => 'documents'
        ])
            ->setExtra('icon', 'fas fa-file-alt');

        $this->wikiMenu($menu);

        if($this->sickNoteSettings->isEnabled() === true) {
            if($this->authorizationChecker->isGranted('ROLE_SICK_NOTE_VIEWER')
            || $this->authorizationChecker->isGranted('ROLE_SICK_NOTE_CREATOR')
            || $this->authorizationChecker->isGranted(SickNoteVoter::New)) {
                $menu->addChild('sick_notes.label', [
                    'route' => 'sick_notes'
                ])
                    ->setExtra('icon', 'fas fa-clinic-medical');
            }
        }

        if($this->authorizationChecker->isGranted('ROLE_BOOK_VIEWER')) {
            $this->bookMenu($menu);
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

        $enabledFor = $this->notificationSettings->getEmailEnabledUserTypes();

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
                'route' => 'profile_notifications'
            ])
                ->setExtra('icon', 'far fa-bell')
                ->setExtra('pull-right', true)
                ->setAttribute('title', $this->translator->trans('profile.notifications.label'));
        }

        return $root;
    }
}
