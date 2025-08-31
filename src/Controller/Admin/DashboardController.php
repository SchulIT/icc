<?php

namespace App\Controller\Admin;

use App\Entity\Absence;
use App\Entity\Exam;
use App\Entity\FreeTimespan;
use App\Entity\Grade;
use App\Entity\Infotext;
use App\Entity\LearningManagementSystem;
use App\Entity\PrivacyCategory;
use App\Entity\Room;
use App\Entity\Student;
use App\Entity\StudentLearningManagementSystemInformation;
use App\Entity\StudyGroup;
use App\Entity\Substitution;
use App\Entity\TimetableLesson;
use App\Entity\TimetableSupervision;
use App\Entity\Tuition;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
            MenuItem::linkToCrud('Schülerinnen und Schüler', 'fas fa-user-graduate', Student::class),
            MenuItem::linkToCrud('Klassen', 'fas fa-users', Grade::class),
            MenuItem::linkToCrud('Lerngruppen', 'fas fa-users', StudyGroup::class),
            //MenuItem::linkToCrud('StudyGroupMembership', '', StudyGroupMembership::class),
            MenuItem::linkToCrud('Unterrichte', 'fas fa-chalkboard-teacher', Tuition::class),

            MenuItem::section('SchILD-NRW'),
            MenuItem::linkToCrud('Datenschutzkategorien', 'fas fa-user-shield', PrivacyCategory::class),
            MenuItem::linkToCrud('Lernplattformen', 'fas fa-mail-bulk', LearningManagementSystem::class),
            MenuItem::linkToCrud('Lernplattform-Zustimmungen', 'fas fa-mail-bulk', StudentLearningManagementSystemInformation::class),

            MenuItem::section('Vertretungsplan'),
            MenuItem::linkToCrud('Absenzen', 'fas fa-user-times', Absence::class),
            MenuItem::linkToCrud('Unterrichtsfreie Zeiten', 'far fa-calendar-times', FreeTimespan::class),
            MenuItem::linkToCrud('Tagestexte', 'fas fa-info-circle', Infotext::class),
            MenuItem::linkToCrud('Vertretungen', 'fas fa-random', Substitution::class),

            MenuItem::section('Stundenplan'),
            MenuItem::linkToCrud('Räume', 'fas fa-door-open', Room::class),
            MenuItem::linkToCrud('Stundenplanstunden', 'fas fa-clock', TimetableLesson::class),
            MenuItem::linkToCrud('Aufsichten', 'fas fa-eye', TimetableSupervision::class),

            MenuItem::section('Klausurplan'),
            MenuItem::linkToCrud('Klausuren', 'fas fa-edit', Exam::class),

            MenuItem::section('Sonstiges'),
            MenuItem::linkToCrud('Benutzer', 'fas fa-users', User::class),

        ];
    }
}
