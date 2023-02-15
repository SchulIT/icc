<?php

namespace App\Controller\Admin;

use App\Entity\Absence;
use App\Entity\Exam;
use App\Entity\FreeTimespan;
use App\Entity\Grade;
use App\Entity\GradeMembership;
use App\Entity\GradeTeacher;
use App\Entity\Infotext;
use App\Entity\LearningManagementSystem;
use App\Entity\Room;
use App\Entity\StudentAbsence;
use App\Entity\Student;
use App\Entity\StudentLearningManagementSystemInformation;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
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
use Symfony\Component\Routing\Annotation\Route;

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
            MenuItem::linkToCrud('Absence', 'fas fa-folder-open', Absence::class),
            MenuItem::linkToCrud('Exam', 'fas fa-folder-open', Exam::class),
            MenuItem::linkToCrud('FreeTimespan', 'fas fa-folder-open', FreeTimespan::class),
            MenuItem::linkToCrud('Grade', 'fas fa-folder-open', Grade::class),
            MenuItem::linkToCrud('GradeTeacher', 'fas fa-folder-open', GradeTeacher::class),
            MenuItem::linkToCrud('GradeMembership', 'fas fa-folder-open', GradeMembership::class),
            MenuItem::linkToCrud('Infotext', 'fas fa-folder-open', Infotext::class),
            MenuItem::linkToCrud('Room', 'fas fa-folder-open', Room::class),
            MenuItem::linkToCrud('Student', 'fas fa-folder-open', Student::class),
            MenuItem::linkToCrud('StudentAbsence', 'fas fa-folder-open', StudentAbsence::class),
            MenuItem::linkToCrud('StudyGroup', 'fas fa-folder-open', StudyGroup::class),
            MenuItem::linkToCrud('StudyGroupMembership', 'fas fa-folder-open', StudyGroupMembership::class),
            MenuItem::linkToCrud('Substitution', 'fas fa-folder-open', Substitution::class),
            MenuItem::linkToCrud('TimetableLesson', 'fas fa-folder-open', TimetableLesson::class),
            MenuItem::linkToCrud('TimetableSupervision', 'fas fa-folder-open', TimetableSupervision::class),
            MenuItem::linkToCrud('Tuition', 'fas fa-folder-open', Tuition::class),
            MenuItem::linkToCrud('User', 'fas fa-folder-open', User::class),
            MenuItem::linkToCrud('Lernplattformen', '', LearningManagementSystem::class),
            MenuItem::linkToCrud('Zustimmungen (SuS)', '', StudentLearningManagementSystemInformation::class)
        ];
    }
}
