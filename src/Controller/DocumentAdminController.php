<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Grouping\DocumentCategoryStrategy;
use App\Grouping\Grouper;
use App\Repository\DocumentRepositoryInterface;
use App\Security\Voter\DocumentVoter;
use App\Sorting\DocumentCategoryStrategy as DocumentCategorySortingStrategy;
use App\Sorting\DocumentNameStrategy;
use App\Sorting\Sorter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/documents")
 */
class DocumentAdminController extends AbstractController {
    private $repository;

    public function __construct(DocumentRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_documents")
     */
    public function index(Sorter $sorter, Grouper $grouper) {
        $documents = [ ];

        foreach($this->repository->findAll() as $document) {
            if($this->isGranted(DocumentVoter::Edit, $document)) {
                $documents[] = $document;
            }
        }

        $categories = $grouper->group($documents, DocumentCategoryStrategy::class);
        $sorter->sort($categories, DocumentCategorySortingStrategy::class);
        $sorter->sortGroupItems($categories, DocumentNameStrategy::class);

        return $this->render('admin/documents/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/add", name="admin_add_document")
     */
    public function add(Request $request) {
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

    /**
     * @Route("/{id}/edit", name="admin_edit_document")
     */
    public function edit(Document $document, Request $request) {
        $this->denyAccessUnlessGranted(DocumentVoter::Edit, $document);

        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($document);

            $this->addFlash('success', 'admin.documents.edit.success');
            return $this->redirectToRoute('admin_documents');
        }

        return $this->render('admin/documents/edit.html.twig', [
            'document' => $document,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/remove", name="admin_remove_document")
     */
    public function remove(Document $document, Request $request) {
        $this->denyAccessUnlessGranted(DocumentVoter::Remove, $document);
    }
}