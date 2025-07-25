<?php

namespace App\Menu;

use App\Entity\User;
use App\Feature\Feature;
use App\Feature\FeatureManager;
use App\Repository\ChatMessageRepositoryInterface;
use App\Repository\ReturnItemRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\WikiArticleRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\ChatVoter;
use App\Security\Voter\ListsVoter;
use App\Security\Voter\ParentsDayAppointmentVoter;
use App\Security\Voter\ResourceReservationVoter;
use App\Security\Voter\StudentAbsenceVoter;
use App\Security\Voter\StudentVoter;
use App\Security\Voter\TeacherAbsenceVoter;
use App\Security\Voter\WikiVoter;
use App\Settings\BookSettings;
use App\Settings\ChatSettings;
use App\Settings\StudentAbsenceSettings;
use App\Settings\TeacherAbsenceSettings;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Builder {
    public function __construct(private readonly FactoryInterface $factory,
                                private readonly AuthorizationCheckerInterface $authorizationChecker,
                                private readonly WikiArticleRepositoryInterface $wikiRepository,
                                private readonly TimetableLessonRepositoryInterface $lessonRepository,
                                private readonly TokenStorageInterface $tokenStorage,
                                private readonly DateHelper $dateHelper,
                                private readonly SectionResolverInterface $sectionResolver,
                                private readonly BookSettings $bookSettings,
                                private readonly ChatMessageRepositoryInterface $chatMessageRepository,
                                private readonly ReturnItemRepositoryInterface $returnItemRepository,
                                private readonly FeatureManager $featureManager)
    {
    }

    public function mainMenu(array $options) {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'navbar-nav me-auto');

        $user = $this->tokenStorage->getToken()?->getUser();

        $menu->addChild('dashboard.label', [
            'route' => 'dashboard'
        ])
            ->setExtra('icon', 'fa fa-home');

        if($this->featureManager->isFeatureEnabled(Feature::Messages)) {
            $menu->addChild('messages.overview.label', [
                'route' => 'messages'
            ])
                ->setExtra('icon', 'fas fa-envelope-open-text');
        }

        if($this->featureManager->isFeatureEnabled(Feature::Chat) && $user instanceof User && $this->authorizationChecker->isGranted(ChatVoter::ChatEnabled)) {
            $menu->addChild('chat.label', [
                'route' => 'chats'
            ])
                ->setExtra('icon', 'fa-solid fa-comments')
                ->setExtra('count', $this->chatMessageRepository->countUnreadMessages($user));
        }

        $this->plansMenu($menu);
        $this->listsMenu($menu);

        if($this->featureManager->isFeatureEnabled(Feature::Documents)) {
            $menu->addChild('documents.label', [
                'route' => 'documents'
            ])
                ->setExtra('icon', 'fas fa-file-alt');
        }

        if($this->featureManager->isFeatureEnabled(Feature::Wiki)) {
            $this->wikiMenu($menu);
        }

        if(($this->featureManager->isFeatureEnabled(Feature::StudentAbsence) || $this->featureManager->isFeatureEnabled(Feature::TeacherAbsence)) && ($this->authorizationChecker->isGranted('ROLE_SICK_NOTE_VIEWER')
            || $this->authorizationChecker->isGranted('ROLE_SICK_NOTE_CREATOR')
            || $this->authorizationChecker->isGranted(StudentAbsenceVoter::New)
            || $this->authorizationChecker->isGranted(TeacherAbsenceVoter::NewAbsence)
            || $this->authorizationChecker->isGranted(TeacherAbsenceVoter::CanViewAny))) {
            $this->absencesMenu($menu);
        }

        if($this->featureManager->isFeatureEnabled(Feature::Book) && $user instanceof User) {
            $this->reportMenu($menu, $user);
        }

        if($this->featureManager->isFeatureEnabled(Feature::ParentsDay) && $this->authorizationChecker->isGranted(ParentsDayAppointmentVoter::VIEW)) {
            $menu->addChild('parents_day.label', [
                'route' => 'parents_day'
            ])
                ->setExtra('icon', 'fa-solid fa-people-arrows');
        }

        if($this->featureManager->isFeatureEnabled(Feature::ReturnItem)) {
            $count = 0;

            if($user instanceof User && $user->isStudentOrParent()) {
                $count = $this->returnItemRepository->countNonReturnedForStudents($user->getStudents()->toArray());
            }

            $menu->addChild('return_items.label', [
                'route' => 'return_items'
            ])
                ->setExtra('icon', 'fa-solid fa-repeat')
                ->setextra('count', $count);
        }

        if($this->featureManager->isFeatureEnabled(Feature::Book) && $this->authorizationChecker->isGranted('ROLE_BOOK_VIEWER')) {
            $this->bookMenu($menu);
        }

        if($this->featureManager->isFeatureEnabled(Feature::Checklists) && $user instanceof User && ($user->isStudentOrParent() || $user->isTeacher())) {
            $menu->addChild('checklists.label', [
                'route' => 'checklists'
            ])
                ->setExtra('icon', 'fa-solid fa-list-check');
        }

        if($this->authorizationChecker->isGranted(StudentVoter::ShowAny)) {
            $menu->addChild('students.label', [
                'route' => 'students'
            ])
                ->setExtra('icon', 'fa-solid fa-users');
        }

        return $menu;
    }

    private function reportMenu(ItemInterface $menu, User $user): ItemInterface {
        if(!$user->isStudentOrParent()) {
            return $menu;
        }

        $reports = $menu->addChild('reports.label')
            ->setExtra('menu', 'reports')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('icon', 'fa fa-chalkboard-user');

        if($this->bookSettings->isLessonTopicsVisibleForStudentsAndParentsEnabled()) {
            $reports->addChild('lesson_topics.label', [
                'route' => 'student_lessons_overview'
            ])
                ->setExtra('icon', 'fa-solid fa-book-open');
        }

        if($this->bookSettings->isAttendanceVisibleForStudentsAndParentsEnabled()) {
            $reports
                ->addChild('attendance.label', [
                'route' => 'student_attendance'
            ])
                ->setExtra('icon', 'fa-solid fa-list-check');
        }

        if($this->bookSettings->getStudentsAndParentsCanViewBookCommentsEnabled()) {
            $reports
                ->addChild('comments.label', [
                    'route' => 'student_comments'
                ])
                ->setExtra('icon', 'fa-regular fa-comments');
        }

        $this->replaceWithFirstItem($menu, $reports, false, false);

        return $menu;
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
                ->setExtra('icon', 'fas fa-user-tie');
        }

        if($this->featureManager->isFeatureEnabled(Feature::Privacy) && $this->authorizationChecker->isGranted(ListsVoter::Privacy)) {
            $lists->addChild('lists.privacy.label', [
                'route' => 'list_privacy'
            ])
                ->setExtra('icon', 'fas fa-user-shield');
        }

        if($this->featureManager->isFeatureEnabled(Feature::LMS) && $this->authorizationChecker->isGranted(ListsVoter::LearningManagementSystems)) {
            $lists->addChild('lists.lms.label', [
                'route' => 'list_lms'
            ])
                ->setExtra('icon', 'fas fa-mail-bulk');
        }

        $this->replaceWithFirstItem($menu, $lists, false, false);

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

        if($this->tokenStorage->getToken() === null) {
            return $book;
        }

        $user = $this->tokenStorage->getToken()->getUser();
        $currentSection = $this->sectionResolver->getCurrentSection();

        if($user instanceof User && $user->getTeacher() !== null && $currentSection !== null) {
            $count = $this->lessonRepository->countMissingByTeacher($user->getTeacher(), $currentSection->getStart(), $this->dateHelper->getToday());
            $missing->setExtra('count', $count);
        }

        $book->addChild('book.students.label', [
            'route' => 'book_students'
        ])
            ->setExtra('icon', 'fas fa-users');

        if($this->featureManager->isFeatureEnabled(Feature::GradeBook)) {
            $book->addChild('book.grades.label', [
                'route' => 'gradebook'
            ])
                ->setExtra('icon', 'fas fa-user-graduate');
        }

        $book->addChild('book.excuse_note.label', [
            'route' => 'excuse_notes'
        ])
            ->setExtra('icon', 'fas fa-pen-alt');

        $book->addChild('book.export.label', [
            'route' => 'book_export'
        ])
            ->setExtra('icon', 'fas fa-download');

        $book->addChild('book.integrity_check.label', [
            'route' => 'book_integrity_check'
        ])
            ->setExtra('icon', 'fas fa-clipboard-check');

        return $book;
    }

    private function absencesMenu(ItemInterface $menu): ItemInterface {
        $plans = $menu->addChild('absences.label')
            ->setExtra('menu', 'absences')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('icon', 'fas fa-user-times');

        if($this->featureManager->isFeatureEnabled(Feature::StudentAbsence)) {
            if ($this->authorizationChecker->isGranted('ROLE_SICK_NOTE_VIEWER')
                || $this->authorizationChecker->isGranted('ROLE_SICK_NOTE_CREATOR')
                || $this->authorizationChecker->isGranted(StudentAbsenceVoter::New)) {
                $plans->addChild('absences.students.label', [
                    'route' => 'student_absences'
                ])
                    ->setExtra('icon', 'fas fa-user-graduate');
            }

            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $plans->addChild('absences.students.export.label', [
                    'route' => 'export_student_absences'
                ])
                    ->setExtra('icon', 'fas fa-download');
            }
        }

        if($this->featureManager->isFeatureEnabled(Feature::TeacherAbsence) && ($this->authorizationChecker->isGranted(TeacherAbsenceVoter::NewAbsence) || $this->authorizationChecker->isGranted(TeacherAbsenceVoter::CanViewAny))) {
            $plans->addChild('absences.teachers.label', [
                'route' => 'teacher_absences'
            ])
                ->setExtra('icon', 'fas fa-chalkboard-teacher');
        }

        $this->replaceWithFirstItem($menu, $plans, true, true);

        return $plans;
    }

    private function replaceWithFirstItem(ItemInterface $menu, ItemInterface &$item, bool $keepParentIcon, bool $keepParentLabel): void {
        if(count($item->getChildren()) !== 1) {
            return;
        }

        $firstItem = $item->getFirstChild();
        $firstItem->setParent(null);

        if($keepParentIcon === true) {
            $firstItem->setExtra('icon', $item->getExtra('icon'));
        }

        if($keepParentLabel === true) {
            $firstItem->setLabel($item->getLabel());
        }

        $menu->removeChild($item);
        $menu->addChild($firstItem);
    }
}
