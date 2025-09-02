<?php

namespace App\Controller\Admin;

use App\Entity\Gender;
use App\Entity\Student;
use App\Form\StudentLearningManagementSystemInformationType;
use App\Repository\LessonAttendanceRepositoryInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudentCrudController extends AbstractCrudController
{

    public function __construct(private readonly LessonAttendanceRepositoryInterface $attendanceRepository, private readonly AdminUrlGenerator $urlGenerator, private readonly AdminUrlGenerator $adminUrlGenerator) {

    }

    public static function getEntityFqcn(): string
    {
        return Student::class;
    }

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('externalId')
            ->add('uuid')
            ->add('firstname')
            ->add('lastname')
            ->add('gender')
            ->add('status')
            ->add('sections');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Schülerin/Schüler')
            ->setEntityLabelInPlural('Schülerinnen und Schüler')
            ->setSearchFields(['externalId', 'firstname', 'lastname', 'gender', 'email', 'status', 'id', 'uuid']);
    }

    public function configureActions(Actions $actions): Actions {
        $removeAttendanceAction = Action::new('removeAttendance', 'Alle Anwesenheiten löschen')
            ->linkToCrudAction('removeAttendance')
            ->displayIf(function(Student $student) {
                return $this->attendanceRepository->countAnyByStudent($student) > 0;
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $removeAttendanceAction)
            ->update(Crud::PAGE_INDEX, Action::DELETE, function(Action $action) {
                return $action->displayIf(function(Student $student) {
                    return $this->attendanceRepository->countAnyByStudent($student) === 0;
                });
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('externalId')->setLabel('Externe ID'),
            TextField::new('firstname')->setLabel('Vorname'),
            TextField::new('lastname')->setLabel('Nachname'),
            ChoiceField::new('gender')
                ->setChoices(Gender::cases())
                ->setLabel('Geschlecht'),
            EmailField::new('email')->setLabel('E-Mail-Adresse'),
            TextField::new('status')->setLabel('Status'),
            DateField::new('birthday')->setLabel('Geburtstag')->hideOnIndex(),
            AssociationField::new('sections')
                ->setLabel('Abschnitte')
                ->setFormTypeOption('expanded', true)
                ->hideOnIndex(),
            AssociationField::new('approvedPrivacyCategories')
                ->setLabel('Zugestimmte Datenschutzkategorien')
                ->setFormTypeOption('expanded', true)
                ->hideOnIndex(),
            CollectionField::new('learningManagementSystems')
                ->setLabel('Lernplattformen')
                ->setEntryType(StudentLearningManagementSystemInformationType::class)
                ->hideOnIndex()
                ->allowAdd(true)
                ->allowDelete(true)
                ->setFormTypeOption('by_reference', false)
        ];
    }

    public function removeAttendance(AdminContext $context, Request $request, TranslatorInterface $translator): Response {
        /** @var Student $student */
        $student = $context->getEntity()->getInstance();

        $form = $this->createForm(ConfirmType::class, [], [
            'message' => 'admin.ea.students.remove_attendance.confirm',
            'message_parameters' => [
                '%lastname%' => $student->getLastname(),
                '%firstname%' => $student->getFirstname()
            ]
        ])
            ->add('submit', SubmitType::class, [
                'label' => 'actions.confirm'
            ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $count = $this->attendanceRepository->removeAnyStudentAttendance($student);
            $this->addFlash('success', $translator->trans(
                'admin.ea.students.remove_attendance.success',
                [
                    '%lastname%' => $student->getLastname(),
                    '%firstname%' => $student->getFirstname(),
                    '%count%' => $count
                ]
            ));

            return $this->redirect(
                $this->adminUrlGenerator
                    ->setController(StudentCrudController::class)
                    ->setAction(Action::INDEX)
                    ->generateUrl()
            );
        }

        return $this->render('admin/ea/form.html.twig', [
            'header' => 'admin.ea.students.remove_attendance.label',
            'form' => $form->createView()
        ]);
    }
}
