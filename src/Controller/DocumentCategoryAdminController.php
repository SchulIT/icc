<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Entity\DocumentCategory;
use App\Form\DocumentCategoryType;
use App\Repository\DocumentCategoryRepositoryInterface;
use App\Sorting\DocumentCategoryNameStrategy;
use App\Sorting\Sorter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/documents/categories')]
#[Security("is_granted('ROLE_DOCUMENTS_ADMIN')")]
class DocumentCategoryAdminController extends AbstractController {

    public function __construct(private DocumentCategoryRepositoryInterface $repository, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);
    }

    #[Route(path: '', name: 'admin_document_categories')]
    public function index(Sorter $sorter): Response {
        $categories = $this->repository->findAll();
        $sorter->sort($categories, DocumentCategoryNameStrategy::class);

        return $this->render('admin/documents/categories/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route(path: '/add', name: 'admin_add_document_category')]
    public function add(Request $request): Response {
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

    #[Route(path: '/{uuid}/edit', name: 'admin_edit_document_category')]
    public function edit(DocumentCategory $documentCategory, Request $request): Response {
        $form = $this->createForm(DocumentCategoryType::class, $documentCategory);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($documentCategory);

            $this->addFlash('success', 'admin.documents.categories.edit.success');

            return $this->redirectToRoute('admin_document_categories');
        }

        return $this->render('admin/documents/categories/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $documentCategory
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'admin_remove_document_category')]
    public function remove(DocumentCategory $category, Request $request, TranslatorInterface $translator): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $translator->trans('admin.documents.categories.remove.confirm', [
                '%name%' => $category->getName()
            ])
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($category);

            $this->addFlash('success', 'admin.documents.categories.remove.success');

            return $this->redirectToRoute('admin_document_categories');
        }

        return $this->render('admin/documents/categories/remove.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }
}