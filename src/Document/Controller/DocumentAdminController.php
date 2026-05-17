<?php

namespace App\Document\Controller;

use App\Common\Entity\User;
use App\Common\Section\SectionResolverInterface;
use App\Common\View\Filter\GradeFilter;
use App\Common\View\Filter\UserTypeFilter;
use App\Document\Entity\Document;
use App\Document\Form\DocumentType;
use App\Document\Grouping\DocumentCategoryStrategy;
use App\Document\Repository\DocumentCategoryRepositoryInterface;
use App\Document\Repository\DocumentRepositoryInterface;
use App\Document\Sorting\DocumentCategoryGroupStrategy;
use App\Document\Sorting\DocumentNameStrategy;
use App\Document\View\Filter\DocumentCategoryFilter;
use App\Document\Voter\DocumentVoter;
use App\Framework\Controller\AbstractController;
use App\Framework\Feature\Feature;
use App\Framework\Feature\IsFeatureEnabled;
use App\Framework\Grouping\Grouper;
use App\Framework\Repository\PaginationQuery;
use App\Framework\Sorting\Sorter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/documents')]
#[IsFeatureEnabled(Feature::Documents)]
class DocumentAdminController extends AbstractController {

    public function __construct(
        private DocumentRepositoryInterface $repository,
        RefererHelper $refererHelper
    ) {
        parent::__construct($refererHelper);
    }

    #[Route(path: '', name: 'admin_documents')]
    public function index(
        UserTypeFilter $userTypeFilter,
        GradeFilter $gradeFilter,
        DocumentCategoryFilter $documentCategoryFilter,
        SectionResolverInterface $sectionResolver,
        #[CurrentUser] User $user,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter(name: 'user_type', filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $userTypeValue = null,
        #[MapQueryParameter(name: 'grade', filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $gradeUuid = null,
        #[MapQueryParameter(name: 'category', filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $categoryUuid = null,
        #[MapQueryParameter] string|null $query = null,
    ): Response {
        $this->denyAccessUnlessGranted(DocumentVoter::Admin);

        $userTypeFilterView = $userTypeFilter->handle($userTypeValue);
        $gradeFilterView = $gradeFilter->handle($gradeUuid, $sectionResolver->getCurrentSection(), $user);
        $categoryFilterView = $documentCategoryFilter->handle($categoryUuid);

        $documents = $this->repository->findPaginated(
            new PaginationQuery(page: $page),
            $userTypeFilterView->getCurrentType(),
            $gradeFilterView->getCurrentGrade(),
            $categoryFilterView->currentCategory,
            $query
        );

        return $this->render('admin/documents/index.html.twig', [
            'categoryFilter' => $categoryFilterView,
            'userTypeFilter' => $userTypeFilterView,
            'gradeFilter' => $gradeFilterView,
            'query' => $query,
            'documents' => $documents,
        ]);
    }

    #[Route(path: '/add', name: 'admin_add_document')]
    public function add(Request $request): Response {
        $this->denyAccessUnlessGranted(DocumentVoter::New);

        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($document);

            $this->addFlash('success', 'admin.documents.add.success');
            return $this->redirectToRoute('admin_documents');
        }

        return $this->render('admin/documents/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'admin_edit_document')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] Document $document, Request $request): Response {
        $this->denyAccessUnlessGranted(DocumentVoter::Edit, $document);

        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($document);

            $this->addFlash('success', 'admin.documents.edit.success');
            return $this->redirectToReferer(['view' => 'show_document'], 'admin_documents', [ 'uuid' => $document->getUuid() ]);
        }

        return $this->render('admin/documents/edit.html.twig', [
            'document' => $document,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'admin_remove_document')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] Document $document, Request $request, TranslatorInterface $translator): Response {
        $this->denyAccessUnlessGranted(DocumentVoter::Remove, $document);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $translator->trans('admin.documents.remove.confirm', [
                '%name%' => $document->getTitle()
            ])
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($document);

            $this->addFlash('success', 'admin.documents.remove.success');

            return $this->redirectToRoute('admin_documents');
        }

        return $this->render('admin/documents/remove.html.twig', [
            'form' => $form->createView(),
            'document' => $document
        ]);
    }
}