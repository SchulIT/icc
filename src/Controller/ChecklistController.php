<?php

namespace App\Controller;

use App\Entity\Checklist;
use App\Entity\ChecklistStudent;
use App\Entity\User;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Form\ChecklistStudentsType;
use App\Form\ChecklistType;
use App\Form\StudentsType;
use App\Repository\ChecklistRepositoryInterface;
use App\Repository\ChecklistStudentRepositoryInterface;
use App\Security\Voter\ChecklistStudentVoter;
use App\Security\Voter\ChecklistVoter;
use App\Sorting\ChecklistStrategy;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/checklist')]
#[IsFeatureEnabled(Feature::Checklists)]
class ChecklistController extends AbstractController {
    public function __construct(private readonly ChecklistRepositoryInterface $repository, RefererHelper $redirectHelper, private readonly Sorter $sorter) {
        parent::__construct($redirectHelper);
    }

    private function indexForStudentsAndParents(User $user, ChecklistStudentRepositoryInterface $checklistStudentRepository): Response {
        $checklists = [ ];

        foreach($user->getStudents() as $student) {
            $checklists[$student->getId()] = [ ];

            foreach($checklistStudentRepository->findAllByStudent($student) as $checklistStudent) {
                if($this->isGranted(ChecklistStudentVoter::View, $checklistStudent)) {
                    $checklists[$student->getId()][] = $checklistStudent;
                }
            }
        }

        $students = $user->getStudents()->toArray();
        $this->sorter->sort($students, StudentStrategy::class);

        return $this->render('checklists/index_students_parents.html.twig', [
            'checklists' => $checklists,
            'students' => $students
        ]);
    }

    #[Route('', name: 'checklists')]
    public function index(ChecklistStudentRepositoryInterface $checklistStudentRepository): Response {
        /** @var User $user */
        $user = $this->getUser();

        if($user->isStudentOrParent()) {
            return $this->indexForStudentsAndParents($user, $checklistStudentRepository);
        }

        $checklists = $this->repository->findAllByUser($user);

        $this->sorter->sort($checklists, ChecklistStrategy::class);

        $checkedCount = [ ];
        $notCheckedCount = [ ];

        foreach($checklists as $checklist) {
            $checkedCount[$checklist->getId()] = $checklistStudentRepository->countCheckedForChecklist($checklist);
            $notCheckedCount[$checklist->getId()] = $checklistStudentRepository->countNotCheckedForChecklist($checklist);
        }

        return $this->render('checklists/index.html.twig', [
            'checklists' => $checklists,
            'checkedCount' => $checkedCount,
            'notCheckedCount' => $notCheckedCount,
        ]);
    }

    #[Route('/add', name: 'add_checklist')]
    public function add(Request $request): Response {
        $this->denyAccessUnlessGranted(ChecklistVoter::Add);

        $checklist = new Checklist();
        $form = $this->createForm(ChecklistType::class, $checklist);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($checklist);
            $this->addFlash('success', 'checklists.add.success');

            return $this->redirectToRoute('show_checklist', [
                'uuid' => $checklist->getUuid()
            ]);
        }

        return $this->render('checklists/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}', name: 'show_checklist')]
    public function show(Checklist $checklist, Request $request): Response {
        $this->denyAccessUnlessGranted(ChecklistVoter::View, $checklist);

        $form = $this->createForm(ChecklistStudentsType::class, $checklist);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $this->isGranted(ChecklistVoter::Edit, $checklist)) {
            $this->repository->persist($checklist);

            $this->addFlash('success', 'checklists.edit.success');
            return $this->redirectToRoute('show_checklist', [
                'uuid' => $checklist->getUuid()
            ]);
        }

        $studentsForm = $this->createForm(StudentsType::class, [], [
            'multiple' => true,
            'apply_from_studygroups' => true
        ]);
        $studentsForm->handleRequest($request);

        if($studentsForm->isSubmitted() && $studentsForm->isValid() && $this->isGranted(ChecklistVoter::Edit, $checklist)) {
            foreach($studentsForm->getData() as $student) {
                if($checklist->getStudents()->filter(fn(ChecklistStudent $checklistStudent) => $checklistStudent->getStudent()->getUuid() === $student->getUuid())->isEmpty() !== true) {
                    continue;
                }

                $checklist->addStudent((new ChecklistStudent())->setChecklist($checklist)->setStudent($student));
            }

            $this->repository->persist($checklist);
            $this->addFlash('success', 'checklists.students.add.success');
            return $this->redirectToRoute('show_checklist', [
                'uuid' => $checklist->getUuid()
            ]);
        }

        return $this->render('checklists/show.html.twig', [
            'checklist' => $checklist,
            'form' => $form->createView(),
            'studentsForm' => $studentsForm->createView()
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_checklist')]
    public function edit(Checklist $checklist, Request $request): Response {
        $this->denyAccessUnlessGranted(ChecklistVoter::Edit, $checklist);

        $form = $this->createForm(ChecklistType::class, $checklist);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($checklist);
            $this->addFlash('success', 'checklists.edit.success');

            return $this->redirectToRoute('show_checklist', [
                'uuid' => $checklist->getUuid()
            ]);
        }

        return $this->render('checklists/edit.html.twig', [
            'form' => $form->createView(),
            'checklist' => $checklist
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_checklist')]
    public function remove(Checklist $checklist, Request $request): Response {
        $this->denyAccessUnlessGranted(ChecklistVoter::Remove, $checklist);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'checklists.remove.confirm',
            'message_parameters' => [
                '%title%' => $checklist->getTitle(),
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($checklist);
            $this->addFlash('success', 'checklists.remove.success');
            return $this->redirectToRoute('checklists');
        }

        return $this->render('checklists/remove.html.twig', [
            'checklist' => $checklist,
            'form' => $form->createView()
        ]);
    }
}