<?php

namespace App\Controller;

use App\Entity\GradeMembership;
use App\Entity\ReturnItem;
use App\Entity\User;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Form\ReturnItemType;
use App\Repository\ReturnItemRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\ReturnItemVoter;
use App\View\Filter\GradeFilter;
use App\View\Filter\GradeFilterView;
use App\View\Filter\StudentFilter;
use App\View\Filter\StudentFilterView;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/returns')]
#[IsFeatureEnabled(Feature::ReturnItem)]
class ReturnItemController extends AbstractController {

    public function __construct(RefererHelper $redirectHelper, private readonly ReturnItemRepositoryInterface $repository) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'return_items')]
    public function index(StudentFilter $studentFilter, GradeFilter $gradeFilter, Request $request, SectionResolverInterface $sectionResolver, #[CurrentUser] $user): Response {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 25);

        if($limit > 500 || $limit <= 0) {
            $limit = 25;
        }

        $studentFilterView = $studentFilter->handle($request->query->get('student'), $sectionResolver->getCurrentSection(), $user);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionResolver->getCurrentSection(), $user);

        $items = [ ];
        $pages = 1;

        if($user->isStudentOrParent()) {
            $result = $this->repository->findByStudentsPaginated($user->getStudents()->toArray(), $page, $limit);
            // reset filters to prevent displaying them
            $studentFilterView = new StudentFilterView([], null, 0);
            $gradeFilterView = new GradeFilterView([], null, []);
        } else {
            if ($studentFilterView->getCurrentStudent() !== null) {
                $result = $this->repository->findByStudentsPaginated([$studentFilterView->getCurrentStudent()], $page, $limit);
            } else {
                if ($gradeFilterView->getCurrentGrade() !== null) {
                    $students = $gradeFilterView->getCurrentGrade()->getMemberships()
                        ->filter(fn(GradeMembership $membership) => $membership->getSection()?->getId() === $sectionResolver->getCurrentSection()?->getId())
                        ->map(fn(GradeMembership $membership) => $membership->getStudent())
                        ->toArray();

                    $result = $this->repository->findByStudentsPaginated($students, $page, $limit);
                } else {
                    $result = $this->repository->findAllPaginated($page, $limit);
                }
            }
        }

        $items = $result->result;
        $pages = ceil((double)$result->totalCount / $limit);

        return $this->render('returns/index.html.twig', [
            'items' => $items,
            'page' => $page,
            'pages' => $pages,
            'limit' => $limit,
            'studentFilter' => $studentFilterView,
            'gradeFilter' => $gradeFilterView
        ]);
    }

    #[Route('/add', name: 'add_return_item')]
    public function add(Request $request): Response {
        $this->denyAccessUnlessGranted(ReturnItemVoter::New);

        $item = new ReturnItem();
        $form = $this->createForm(ReturnItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($item);
            $this->addFlash('success', 'return_items.add.success');

            return $this->redirectToRoute('return_items', [
                'student' => $item->getStudent()->getUuid()->toString(),
            ]);
        }

        return $this->render('returns/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}', name: 'show_return_item')]
    public function show(ReturnItem $item, SectionResolverInterface $sectionResolver): Response {
        $this->denyAccessUnlessGranted(ReturnItemVoter::Show, $item);

        return $this->render('returns/show.html.twig', [
            'item' => $item,
            'section' => $sectionResolver->getSectionForDate($item->getCreatedAt())
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_return_item')]
    public function edit(ReturnItem $item, Request $request): Response {
        $this->denyAccessUnlessGranted(ReturnItemVoter::Edit, $item);

        $form = $this->createForm(ReturnItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($item);
            $this->addFlash('success', 'return_items.edit.success');

            return $this->redirectToRoute('show_return_item', [
                'uuid' => $item->getUuid()->toString()
            ]);
        }

        return $this->render('returns/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $item
        ]);
    }

    #[Route('/{uuid}/return', name: 'return_return_item')]
    public function return(ReturnItem $item, Request $request): Response {
        $this->denyAccessUnlessGranted(ReturnItemVoter::Return, $item);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'return_items.return.confirm',
            'message_parameters' => [
                '%type%' => $item->getType()->getDisplayName(),
                '%firstname%' => $item->getStudent()->getFirstname(),
                '%lastname%' => $item->getStudent()->getLastname()
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item->setIsReturned(true);
            $this->repository->persist($item);
            $this->addFlash('success', 'return_items.return.success');

            return $this->redirectToRoute('show_return_item', [
                'uuid' => $item->getUuid()->toString()
            ]);
        }

        return $this->render('returns/return.html.twig', [
            'form' => $form->createView(),
            'item' => $item
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_return_item')]
    public function remove(ReturnItem $item, Request $request): Response {
        $this->denyAccessUnlessGranted(ReturnItemVoter::Remove, $item);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'return_items.remove.confirm',
            'message_parameters' => [
                '%type%' => $item->getType()->getDisplayName(),
                '%firstname%' => $item->getStudent()->getFirstname(),
                '%lastname%' => $item->getStudent()->getLastname()
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($item);
            $this->addFlash('success', 'return_items.remove.success');

            return $this->redirectToRoute('return_items');
        }

        return $this->render('returns/remove.html.twig', [
            'form' => $form->createView(),
            'item' => $item
        ]);
    }
}