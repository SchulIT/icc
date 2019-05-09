<?php

namespace App\Controller;

use App\Entity\DocumentCategory;
use App\Form\DocumentCategoryType;
use App\Repository\DocumentCategoryRepositoryInterface;
use App\Sorting\DocumentCategoryNameStrategy;
use App\Sorting\Sorter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/documents/categories")
 * @Security("is_granted('ROLE_DOCUMENTS_ADMIN')")
 */
class DocumentCategoryAdminController extends AbstractController {

    private $repository;

    public function __construct(DocumentCategoryRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_document_categories")
     */
    public function index(Sorter $sorter) {
        $categories = $this->repository->findAll();
        $sorter->sort($categories, DocumentCategoryNameStrategy::class);

        return $this->render('admin/documents/categories/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/add", name="admin_add_document_category")
     */
    public function add(Request $request) {
        $documentCategory = new DocumentCategory();
        $form = $this->createForm(DocumentCategoryType::class, $documentCategory);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($documentCategory);

            $this->addFlash('success', 'admin.documents.categories.add.success');

            return $this->redirectToRoute('admin_document_categories');
        }

        return $this->render('admin/documents/categories/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_edit_document_category")
     */
    public function edit(DocumentCategory $documentCategory, Request $request) {
        $form = $this->createForm(DocumentCategoryType::class, $documentCategory);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($documentCategory);

            $this->addFlash('success', 'admin.documents.categories.edit.success');

            return $this->redirectToRoute('admin_document_categories');
        }

        return $this->render('admin/documents/categories/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/remove", name="admin_remove_document_category")
     */
    public function remove(DocumentCategory $category, Request $request) {

    }
}