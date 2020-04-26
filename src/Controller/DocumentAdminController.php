<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Grouping\DocumentCategoryStrategy;
use App\Grouping\Grouper;
use App\Repository\DocumentRepositoryInterface;
use App\Repository\LogRepositoryInterface;
use App\Security\Voter\DocumentVoter;
use App\Sorting\DocumentCategoryStrategy as DocumentCategorySortingStrategy;
use App\Sorting\DocumentNameStrategy;
use App\Sorting\LogEntryStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use SchoolIT\CommonBundle\Form\ConfirmType;
use SchoolIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/documents")
 */
class DocumentAdminController extends AbstractController {

    private const VersionParam = '_version';
    private const RevertCsrfTokenParam = '_csrf_token';
    private const RevertCsrfToken = 'revert-document';

    private $repository;

    public function __construct(DocumentRepositoryInterface $repository, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);

        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_documents")
     */
    public function index(Sorter $sorter, Grouper $grouper) {
        $this->denyAccessUnlessGranted(DocumentVoter::Admin);

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
     * @Route("/{uuid}/edit", name="admin_edit_document")
     */
    public function edit(Document $document, Request $request) {
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

    /**
     * @Route("/{uuid}/versions", name="document_versions")
     */
    public function versions(Document $document, Request $request, LogRepositoryInterface $logRepository, Sorter $sorter) {
        $this->denyAccessUnlessGranted(DocumentVoter::Edit, $document);

        $logs = $logRepository->getLogEntries($document);
        $sorter->sort($logs, LogEntryStrategy::class, SortDirection::Descending());

        return $this->render('admin/documents/versions.html.twig', [
            'document' => $document,
            'logs' => $logs,
            'token_id' => static::RevertCsrfToken,
            'token_param' => static::RevertCsrfTokenParam,
            'version_param' => static::VersionParam
        ]);
    }

    /**
     * @Route("/{uuid}/versions/{version}", name="show_document_version")
     */
    public function version(Document $document, LogRepositoryInterface $logRepository, int $version) {
        $this->denyAccessUnlessGranted(DocumentVoter::Edit, $document);

        $logs = $logRepository->getLogEntries($document);
        $entry = null;

        foreach($logs as $logEntry) {
            if($logEntry->getVersion() === $version) {
                $entry = $logEntry;
            }
        }

        if($entry === null) {
            throw new NotFoundHttpException();
        }

        $logRepository->revert($document, $version);

        return $this->render('admin/documents/version.html.twig', [
            'document' => $document,
            'entry' => $entry,
            'token_id' => static::RevertCsrfToken,
            'token_param' => static::RevertCsrfTokenParam,
            'version_param' => static::VersionParam
        ]);
    }

    /**
     * @Route("/{uuid}/restore", name="restore_document_version")
     */
    public function restore(Document $document, Request $request, LogRepositoryInterface $logRepository, TranslatorInterface $translator) {
        $this->denyAccessUnlessGranted(DocumentVoter::Edit, $document);

        if($this->isCsrfTokenValid(static::RevertCsrfToken, $request->request->get(static::RevertCsrfTokenParam)) !== true) {
            $this->addFlash('error', $translator->trans('The CSRF token is invalid. Please try to resubmit the form.', [], 'validators'));

            return $this->redirectToRoute('document_versions', [
                'uuid' => $document->getUuid()
            ]);
        }

        $logRepository->revert($document, $request->request->get(static::VersionParam));
        $this->repository->persist($document);

        $this->addFlash('success', 'versions.restore.success');

        return $this->redirectToRoute('show_document', [
            'uuid' => $document->getUuid()
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="admin_remove_document")
     */
    public function remove(Document $document, Request $request, TranslatorInterface $translator) {
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