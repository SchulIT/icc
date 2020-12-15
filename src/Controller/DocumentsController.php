<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\DocumentAttachment;
use App\Entity\User;
use App\Entity\UserType;
use App\Filesystem\DocumentFilesystem;
use App\Filesystem\FileNotFoundException;
use App\Grouping\DocumentCategoryStrategy as DocumentCategoryGroupingStrategy;
use App\Grouping\Grouper;
use App\Repository\DocumentRepositoryInterface;
use App\Security\Voter\DocumentVoter;
use App\Sorting\DocumentCategoryGroupStrategy;
use App\Sorting\DocumentNameStrategy;
use App\Sorting\Sorter;
use App\View\Filter\StudyGroupFilter;
use App\View\Filter\UserTypeFilter;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(DocumentRepositoryInterface $documentRepository, StudyGroupFilter $studyGroupFilter, UserTypeFilter $userTypeFilter, Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        $q = $request->query->get('q', null);
        $studyGroupFilterView = $studyGroupFilter->handle($request->query->get('study_group', null), $user, true);
        $userTypeFilterView = $userTypeFilter->handle($request->query->get('user_type', null), $user, $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent()), $user->getUserType());

        $documents = $documentRepository->findAllFor($userTypeFilterView->getCurrentType(), $studyGroupFilterView->getCurrentStudyGroup(), $q);
        $documents = array_filter($documents, function(Document $document) {
            return $this->isGranted(DocumentVoter::View, $document);
        });

        $this->sorter->sort($documents, DocumentNameStrategy::class);
        $categories = $this->grouper->group($documents, DocumentCategoryGroupingStrategy::class);
        $this->sorter->sort($categories, DocumentCategoryGroupStrategy::class);

        return $this->render('documents/index.html.twig', [
            'studyGroupFilter' => $studyGroupFilterView,
            'userTypeFilter' => $userTypeFilterView,
            'categories' => $categories,
            'q' => $q
        ]);
    }

    /**
     * @Route("/attachment/{uuid}", name="download_document_attachment")
     */
    public function downloadAttachment(DocumentAttachment $attachment, DocumentFilesystem $documentFilesystem) {
        $this->denyAccessUnlessGranted(DocumentVoter::View, $attachment->getDocument());

        try {
            return $documentFilesystem->getDownloadResponse($attachment);
        } catch (FileNotFoundException $exception) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/{uuid}", name="show_document")
     */
    public function show(Document $document) {
        $this->denyAccessUnlessGranted(DocumentVoter::View, $document);

        return $this->render('documents/show.html.twig', [
            'document' => $document
        ]);
    }
}