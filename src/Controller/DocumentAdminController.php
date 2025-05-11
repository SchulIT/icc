<?php

namespace App\Controller;

use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Document;
use App\Form\DocumentType;
use App\Grouping\DocumentCategoryStrategy;
use App\Grouping\Grouper;
use App\Repository\DocumentRepositoryInterface;
use App\Repository\LogRepositoryInterface;
use App\Security\Voter\DocumentVoter;
use App\Sorting\DocumentCategoryGroupStrategy;
use App\Sorting\DocumentNameStrategy;
use App\Sorting\LogEntryStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/documents')]
#[IsFeatureEnabled(Feature::Documents)]
class DocumentAdminController extends AbstractController {

    public function __construct(private DocumentRepositoryInterface $repository, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);
    }

    #[Route(path: '', name: 'admin_documents')]
    public function index(Sorter $sorter, Grouper $grouper): Response {
        $this->denyAccessUnlessGranted(DocumentVoter::Admin);

        $documents = [ ];

        foreach($this->repository->findAll() as $document) {
            if($this->isGranted(DocumentVoter::Edit, $document)) {
                $documents[] = $document;
            }
        }

        $categories = $grouper->group($documents, DocumentCategoryStrategy::class);
        $sorter->sort($categories, DocumentCategoryGroupStrategy::class);
        $sorter->sortGroupItems($categories, DocumentNameStrategy::class);

        return $this->render('admin/documents/index.html.twig', [
            'categories' => $categories
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