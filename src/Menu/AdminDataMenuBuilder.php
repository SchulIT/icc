<?php

namespace App\Menu;

use App\Security\Voter\ExamVoter;
use Knp\Menu\ItemInterface;

class AdminDataMenuBuilder extends AbstractMenuBuilder {
    public function dataMenu(array $options = []): ItemInterface {
        $root = $this->factory->createItem('root');

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $root->addChild('admin.settings.general.label', [
                'route' => 'admin_settings_general'
            ])
                ->setExtra('icon', 'fas fa-wrench');

            $root->addChild('admin.settings.import.label', [
                'route' => 'admin_settings_import'
            ])
                ->setExtra('icon', 'fas fa-upload');
        }

        if($this->authorizationChecker->isGranted('ROLE_DOCUMENTS_ADMIN') || $this->authorizationChecker->isGranted('ROLE_MESSAGE_CREATOR') || $this->authorizationChecker->isGranted('ROLE_WIKI_ADMIN')) {
            $root->addChild('admin.headers.information', [])
                ->setExtra('isHeader', true);
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

        if($this->authorizationChecker->isGranted('ROLE_WIKI_ADMIN')) {
            $root->addChild('admin.wiki.label', [
                'route' => 'admin_wiki'
            ])
                ->setExtra('icon', 'fab fa-wikipedia-w');
        }

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $root->addChild('admin.headers.core', [])
                ->setExtra('isHeader', true);

            $root->addChild('admin.sections.label', [
                'route' => 'admin_sections'
            ])
                ->setExtra('icon', 'fas fa-sliders-h');

            $root->addChild('admin.subjects.label', [
                'route' => 'admin_subjects'
            ])
                ->setExtra('icon', 'fas fa-graduation-cap');

            $root->addChild('admin.teachers.label', [
                'route' => 'admin_teachers'
            ])
                ->setExtra('icon', 'fas fa-sort-alpha-down');

            $root->addChild('admin.resources.label', [
                'route' => 'admin_resources'
            ])
                ->setExtra('icon', 'fas fa-laptop-house');
        }

        if($this->authorizationChecker->isGranted('ROLE_APPOINTMENT_CREATOR')) {
            $root->addChild('admin.headers.appointments', [])
                ->setExtra('isHeader', true);

            if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $root->addChild('appointment_settings', [
                    'route' => 'admin_settings_appointments',
                    'label' => 'admin.settings.label'
                ])
                    ->setExtra('icon', 'fa-solid fa-sliders');
            }

            $root->addChild('appointments', [
                'route' => 'admin_appointments',
                'label' => 'admin.appointments.label'
            ])
                ->setExtra('icon', 'far fa-calendar');
        }


        if($this->authorizationChecker->isGranted(ExamVoter::Manage)) {
            $root->addChild('admin.headers.exams', [])
                ->setExtra('isHeader', true);

            if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $root->addChild('admin.settings.exams.label', [
                    'route' => 'admin_settings_exams',
                    'label' => 'admin.settings.label'
                ])
                    ->setExtra('icon', 'fa-solid fa-sliders');
            }

            $root->addChild('admin.exams.label', [
                'route' => 'admin_exams'
            ])
                ->setExtra('icon', 'fas fa-pen');
        }

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $root->addChild('admin.headers.timetable', [])
                ->setExtra('isHeader', true);

            $root->addChild('timetable_settings', [
                'route' => 'admin_settings_timetable',
                'label' => 'admin.settings.label'
            ])
                ->setExtra('icon', 'fa-solid fa-sliders');

            $root->addChild('timetable', [
                'route' => 'admin_timetable',
                'label' => 'admin.timetable.weeks.label'
            ])
                ->setExtra('icon', 'far fa-clock');

            $root->addChild('admin.headers.substitutions', [])
                ->setExtra('isHeader', true);

            $root->addChild('substitution_settings', [
                'route' => 'admin_settings_substitutions',
                'label' => 'admin.settings.label'
            ])
                ->setExtra('icon', 'fa-solid fa-sliders');
        }

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $root->addChild('admin.headers.features', [])
                ->setExtra('isHeader', true);

            $root->addChild('admin.settings.dashboard.label', [
                'route' => 'admin_settings_dashboard'
            ])
                ->setExtra('icon', 'fas fa-home');

            $root->addChild('admin.settings.notifications.label', [
                'route' => 'admin_settings_notifications'
            ])
                ->setExtra('icon', 'fas fa-bell');

            $root->addChild('admin.displays.label', [
                'route' => 'admin_displays'
            ])
                ->setExtra('icon', 'fas fa-tv');

            $root->addChild('admin.parents_day.label', [
                'route' => 'admin_parents_days'
            ])
                ->setExtra('icon', 'fa-solid fa-people-arrows');

            $root->addChild('admin.headers.absence', [])
                ->setExtra('isHeader', true);

            $root->addChild('admin.settings.student_absences.label', [
                'route' => 'admin_settings_absences',
                'label' => 'admin.settings.label'
            ])
                ->setExtra('icon', 'fa-solid fa-sliders');

            $root->addChild('admin.absence_types.label_students', [
                'route' => 'admin_absence_types',
                'label' => 'admin.absence_types.label'
            ])
                ->setExtra('icon', 'fas fa-user-times')
                ->setExtra('badge', 'label.students_simple');

            $root->addChild('admin.absence_types.label_teachers', [
                'route' => 'admin_teacher_absence_types',
                'label' => 'admin.absence_types.label'
            ])
                ->setExtra('icon', 'fas fa-user-times')
                ->setExtra('badge', 'label.teachers_simple');
        }

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $root->addChild('admin.headers.book', [])
                ->setExtra('isHeader', true);

            $root->addChild('book_settings', [
                'route' => 'admin_settings_book',
                'label' => 'admin.settings.label'
            ])
                ->setExtra('icon', 'fa-solid fa-sliders');

            $root->addChild('admin.tuition_grades.label', [
                'route' => 'admin_tuition_grades'
            ])
                ->setExtra('icon', 'fas fa-user-graduate');

            $root->addChild('admin.settings.tuition_grades.label', [
                'route' => 'admin_settings_gradebook'
            ])
                ->setExtra('icon', 'fa-solid fa-sliders');

            $root->addChild('admin.attendance_flags.label', [
                'route' => 'admin_attendance_flags'
            ])
                ->setExtra('icon', 'fas fa-list-check');

            $root->addChild('admin.headers.chat', [])
                ->setExtra('isHeader', true);

            $root->addChild('chat_settings', [
                'route' => 'admin_settings_chat',
                'label' => 'admin.settings.label'
            ])
                ->setExtra('icon', 'fa-solid fa-sliders');

            $root->addChild('admin.chat.tags.label', [
                'route' => 'admin_chat_tags'
            ])
                ->setExtra('icon', 'fa-solid fa-user-tag');


        }

        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $root->addChild('admin.headers.imported', [])
                ->setExtra('isHeader', true);

            $root->addChild('admin.ea.label', [
                'uri' => '/admin/ea'
            ])
                ->setLinkAttribute('target', '_blank')
                ->setExtra('icon', 'fas fa-tools');
        }

        return $root;
    }
}