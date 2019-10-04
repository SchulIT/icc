<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\DocumentAttachment;
use App\Entity\User;
use App\Filesystem\DocumentFilesystem;
use App\Filesystem\FileNotFoundException;
use App\Grouping\DocumentCategoryStrategy as DocumentCategoryGroupingStrategy;
use App\Grouping\Grouper;
use App\Repository\DocumentRepositoryInterface;
use App\Security\Voter\DocumentVoter;
use App\Sorting\DocumentCategoryStrategy;
use App\Sorting\DocumentNameStrategy;
use App\Sorting\Sorter;
use App\Utils\RefererHelper;
use App\View\Filter\StudyGroupFilter;
use App\View\Filter\UserTypeFilter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/documents")
 */
class DocumentsController extends AbstractController {

    private $grouper;
    private $sorter;

    public function __construct(Grouper $grouper, Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);

        $this->grouper = $grouper;
        $this->sorter = $sorter;
    }

    /**
     * @Route("", name="documents")
     */
    public function index(DocumentRepositoryInterface $documentRepository, StudyGroupFilter $studyGroupFilter, UserTypeFilter $userTypeFilter,
                          ?int $studyGroupId = null, ?string $userType = null, ?string $q = null) {
        /** @var User $user */
        $user = $this->getUser();

        $studyGroupFilterView = $studyGroupFilter->handle($studyGroupId, $user, true);
        $userTypeFilterView = $userTypeFilter->handle($userType, $user);

        $documents = $documentRepository->findAllFor($userTypeFilterView->getCurrentType(), $studyGroupFilterView->getCurrentStudyGroup(), $q);

        $this->sorter->sort($documents, DocumentNameStrategy::class);
        $categories = $this->grouper->group($documents, DocumentCategoryGroupingStrategy::class);
        $this->sorter->sort($categories, DocumentCategoryStrategy::class);

        return $this->render('documents/index.html.twig', [
            'studyGroupFilter' => $studyGroupFilterView,
            'userTypeFilter' => $userTypeFilterView,
            'categories' => $categories,
            'q' => $q
        ]);
    }

    /**
     * @Route("/{id}/{alias}", name="show_document", requirements={"id": "\d+"})
     */
    public function show(Document $document) {
        $this->denyAccessUnlessGranted(DocumentVoter::View, $document);

        return $this->render('documents/show.html.twig', [
            'document' => $document
        ]);
    }

    /**
     * @Route("/{document}/{alias}/attachment/{id}", name="download_document_attachment", requirements={"id": "\d+"})
     */
    public function downloadAttachment(DocumentAttachment $attachment, DocumentFilesystem $documentFilesystem) {
        $this->denyAccessUnlessGranted(DocumentVoter::View, $attachment->getDocument());

        try {
            return $documentFilesystem->getDownloadResponse($attachment);
        } catch (FileNotFoundException $exception) {
            throw new NotFoundHttpException();
        }
    }
}