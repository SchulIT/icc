<?php

namespace App\Common\Admin;

use App\Exam\Admin\ExamCrudController;
use App\LearningManagementSystem\Admin\LearningManagementSystemCrudController;
use App\Privacy\Admin\PrivacyCategoryCrudController;
use App\Substitution\Admin\AbsenceCrudController;
use App\Substitution\Admin\FreeTimespanCrudController;
use App\Substitution\Admin\InfotextCrudController;
use App\Substitution\Admin\SubstitutionCrudController;
use App\Timetable\Admin\TimetableLessonCrudController;
use App\Timetable\Admin\TimetableSupervisionCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AdminDashboard]
class DashboardController extends AbstractDashboardController
{
    #[Route(path: '/admin/ea')]
    public function index(): Response {
        return $this->render('admin/ea/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Information & Communication Center');
    }

    public function configureCrud(): Crud
    {
        return Crud::new();
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::section('Stammdaten Schulverwaltung'),
            MenuItem::linkTo(StudentCrudController::class, 'Schülerinnen und Schüler', 'fas fa-user-graduate'),
            MenuItem::linkTo(GradeCrudController::class, 'Klassen', 'fas fa-users'),
            MenuItem::linkTo(StudyGroupCrudController::class, 'Lerngruppen', 'fas fa-users'),
            //MenuItem::linkToCrud('StudyGroupMembership', '', StudyGroupMembership::class),
            MenuItem::linkTo(TuitionCrudController::class, 'Unterrichte', 'fas fa-chalkboard-teacher'),

            MenuItem::section('SchILD-NRW'),
            MenuItem::linkTo(PrivacyCategoryCrudController::class, 'Datenschutzkategorien', 'fas fa-user-shield'),
            MenuItem::linkTo(LearningManagementSystemCrudController::class, 'Lernplattformen', 'fas fa-mail-bulk'),
            MenuItem::linkTo(StudentLearningManagementSystemInformationCrudController::class, 'Lernplattform-Zustimmungen', 'fas fa-mail-bulk'),

            MenuItem::section('Vertretungsplan'),
            MenuItem::linkTo(AbsenceCrudController::class, 'Absenzen', 'fas fa-user-times'),
            MenuItem::linkTo(FreeTimespanCrudController::class, 'Unterrichtsfreie Zeiten', 'far fa-calendar-times'),
            MenuItem::linkTo(InfotextCrudController::class, 'Tagestexte', 'fas fa-info-circle'),
            MenuItem::linkTo(SubstitutionCrudController::class, 'Vertretungen', 'fas fa-random'),

            MenuItem::section('Stundenplan'),
            MenuItem::linkTo(RoomCrudController::class, 'Räume', 'fas fa-door-open'),
            MenuItem::linkTo(TimetableLessonCrudController::class, 'Stundenplanstunden', 'fas fa-clock'),
            MenuItem::linkTo(TimetableSupervisionCrudController::class, 'Aufsichten', 'fas fa-eye'),

            MenuItem::section('Klausurplan'),
            MenuItem::linkTo(ExamCrudController::class, 'Klausuren', 'fas fa-edit'),

            MenuItem::section('Sonstiges'),
            MenuItem::linkTo(UserCrudController::class, 'Benutzer', 'fas fa-users'),

        ];
    }
}
