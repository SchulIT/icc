<?php

namespace App\Menu;

use App\Security\Voter\ExamVoter;
use Knp\Menu\ItemInterface;

class AdminDataMenuBuilder extends AbstractMenuBuilder {
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
            $root->addChild('admin.timetable.label', [
                'route' => 'admin_timetable'
            ])
                ->setExtra('icon', 'far fa-clock');

            $root->addChild('admin.resources.label', [
                'route' => 'admin_resources'
            ])
                ->setExtra('icon', 'fas fa-laptop-house');

            $root->addChild('admin.teachers.label', [
                'route' => 'admin_teachers'
            ])
                ->setExtra('icon', 'fas fa-sort-alpha-down');

            $root->addChild('admin.absence_types.label_students', [
                'route' => 'admin_absence_types'
            ])
                ->setExtra('icon', 'fas fa-user-times');

            $root->addChild('admin.absence_types.label_teachers', [
                'route' => 'admin_teacher_absence_types'
            ])
                ->setExtra('icon', 'fas fa-user-times');
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

            $root->addChild('admin.tuition_grades.label', [
                'route' => 'admin_tuition_grades'
            ])
                ->setExtra('icon', 'fas fa-user-graduate');

            $root->addChild('admin.attendance_flags.label', [
                'route' => 'admin_attendance_flags'
            ])
                ->setExtra('icon', 'fas fa-list-check');

            $root->addChild('admin.parents_day.label', [
                'route' => 'admin_parents_days'
            ])
                ->setExtra('icon', 'fa-solid fa-people-arrows');

            $root->addChild('admin.chat.tags.label', [
                'route' => 'admin_chat_tags'
            ])
                ->setExtra('icon', 'fa-solid fa-user-tag');
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
}