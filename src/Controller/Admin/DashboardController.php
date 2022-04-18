<?php

namespace App\Controller\Admin;

use App\Entity\Absence;
use App\Entity\Exam;
use App\Entity\FreeTimespan;
use App\Entity\Grade;
use App\Entity\GradeMembership;
use App\Entity\GradeTeacher;
use App\Entity\Infotext;
use App\Entity\Room;
use App\Entity\SickNote;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\Substitution;
use App\Entity\TimetableLesson;
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
    /**
     * @Route("/admin/ea")
     */
    public function index(): Response {
        return parent::index();
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
        yield MenuItem::linkToCrud('Absence', 'fas fa-folder-open', Absence::class);
        yield MenuItem::linkToCrud('Exam', 'fas fa-folder-open', Exam::class);
        yield MenuItem::linkToCrud('FreeTimespan', 'fas fa-folder-open', FreeTimespan::class);
        yield MenuItem::linkToCrud('Grade', 'fas fa-folder-open', Grade::class);
        yield MenuItem::linkToCrud('GradeTeacher', 'fas fa-folder-open', GradeTeacher::class);
        yield MenuItem::linkToCrud('GradeMembership', 'fas fa-folder-open', GradeMembership::class);
        yield MenuItem::linkToCrud('Infotext', 'fas fa-folder-open', Infotext::class);
        yield MenuItem::linkToCrud('Room', 'fas fa-folder-open', Room::class);
        yield MenuItem::linkToCrud('SickNote', 'fas fa-folder-open', SickNote::class);
        yield MenuItem::linkToCrud('Student', 'fas fa-folder-open', Student::class);
        yield MenuItem::linkToCrud('StudyGroup', 'fas fa-folder-open', StudyGroup::class);
        yield MenuItem::linkToCrud('StudyGroupMembership', 'fas fa-folder-open', StudyGroupMembership::class);
        yield MenuItem::linkToCrud('Substitution', 'fas fa-folder-open', Substitution::class);
        yield MenuItem::linkToCrud('Tuition', 'fas fa-folder-open', Tuition::class);
        yield MenuItem::linkToCrud('TimetableLesson', 'fas fa-folder-open', TimetableLesson::class);
        yield MenuItem::linkToCrud('User', 'fas fa-folder-open', User::class);


    }
}
