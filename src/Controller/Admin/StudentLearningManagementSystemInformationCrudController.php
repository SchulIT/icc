<?php

namespace App\Controller\Admin;

use App\Entity\StudentLearningManagementSystemInformation;
use App\Import\External\Bildungslogin\BildungsloginImporter;
use App\Import\External\Bildungslogin\ImportRequest as BildungsloginImportRequest;
use App\Import\External\Bildungslogin\ImportRequestType as BildungsloginImportRequestType;
use App\Import\External\WestermannZsv\ImportRequest as WestermannImportRequest;
use App\Import\External\WestermannZsv\ImportRequestType as WestermannImportRequestType;
use App\Import\External\WestermannZsv\WestermannZvsImporter;
use App\Tools\WestermannZvs\CheckRequest;
use App\Tools\WestermannZvs\CheckRequestType;
use App\Tools\WestermannZvs\WestermannZvsChecker;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentLearningManagementSystemInformationCrudController extends AbstractCrudController {

    public function __construct(private readonly AdminUrlGenerator $urlGenerator) {

    }

    public static function getEntityFqcn(): string {
        return StudentLearningManagementSystemInformation::class;
    }

    public function configureCrud(Crud $crud): Crud {
        return $crud
            ->setEntityLabelInSingular('Lernplattform-Zustimmung')
            ->setEntityLabelInPlural('Lernplattform-Zustimmungen');
    }

    public function configureActions(Actions $actions): Actions {
        $biloImportAction = Action::new('importbilo', 'Bildungslogin importieren', 'fas fa-upload')
            ->linkToCrudAction('importBiLo')
            ->createAsGlobalAction();

        $westermannImportAction = Action::new('importwestermann', 'Westermann ZVS importieren', 'fas fa-upload')
            ->linkToCrudAction('importWestermannZvs')
            ->createAsGlobalAction();

        $westermannCheckAction = Action::new('checkwestermann', 'Westermann ZVS abgleichen', 'fas fa-check')
            ->linkToCrudAction('checkWestermann')
            ->createAsGlobalAction();

        return $actions
            ->add(Crud::PAGE_INDEX, $biloImportAction)
            ->add(Crud::PAGE_INDEX, $westermannImportAction)
            ->add(Crud::PAGE_INDEX, $westermannCheckAction);
    }

    public function configureFields(string $pageName): iterable {
        return [
            AssociationField::new('student')
                ->setLabel('Kind'),
            AssociationField::new('lms')
                ->setLabel('Lernplattform'),
            TextField::new('username')
                ->setLabel('Benutzername')
                ->setFormTypeOption('required', false),
            TextField::new('password')
                ->setLabel('Passwort')
                ->setFormTypeOption('required', false),
            BooleanField::new('isConsented')
                ->setLabel('Zustimmung erteilt')
                ->setFormTypeOption('required', false)
        ];
    }

    public function importBiLo(AdminContext $context, Request $request, BildungsloginImporter $importer): Response {
        $importRequest = new BildungsloginImportRequest();
        $form = $this->createForm(BildungsloginImportRequestType::class, $importRequest);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $importer->importAsync($importRequest);

            $this->addFlash('success', 'import.bilo.csv.success');
            return $this->redirect(
                $this->urlGenerator->setController(StudentLearningManagementSystemInformationCrudController::class)->setAction(Action::INDEX)->generateUrl()
            );
        }

        return $this->render('admin/ea/form.html.twig', [
            'form' => $form->createView(),
            'header' => 'import.bilo.csv.label',
            'action' => 'actions.import'
        ]);
    }

    public function importWestermannZvs(Request $request, WestermannZvsImporter $importer): Response {
        $importRequest = new WestermannImportRequest();
        $form = $this->createForm(WestermannImportRequestType::class, $importRequest);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $importer->importAsync($importRequest);
            $this->addFlash('success', 'import.westermann_zvs.success');

            return $this->redirect(
                $this->urlGenerator->setController(StudentLearningManagementSystemInformationCrudController::class)->setAction(Action::INDEX)->generateUrl()
            );
        }

        return $this->render('admin/ea/form.html.twig', [
            'form' => $form->createView(),
            'header' => 'import.westermann_zvs.label',
            'action' => 'actions.import'
        ]);
    }

    public function checkWestermann(Request $request, WestermannZvsChecker $checker): Response {
        $checkRequest = new CheckRequest();
        $form = $this->createForm(CheckRequestType::class, $checkRequest);
        $form->handleRequest($request);

        $result = null;

        if($form->isSubmitted() && $form->isValid()) {
            $result = $checker->check($checkRequest);
        }

        return $this->render('admin/ea/westermann_zsv_check.html.twig', [
            'form' => $form->createView(),
            'header' => 'check.westermann_zsv.label',
            'action' => 'actions.check',
            'result' => $result
        ]);
    }
}