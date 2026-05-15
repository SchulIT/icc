<?php

namespace App\Document\Controller;

use App\Framework\Controller\AbstractController;
use App\Framework\Feature\Feature;
use App\Framework\Feature\IsFeatureEnabled;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use App\Document\Entity\Document;
use App\Document\Entity\DocumentAttachment;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Document\Filesystem\DocumentFilesystem;
use App\Framework\Filesystem\FileNotFoundException;
use App\Document\Grouping\DocumentCategoryStrategy as DocumentCategoryGroupingStrategy;
use App\Framework\Grouping\Grouper;
use App\Document\Repository\DocumentRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\Document\Voter\DocumentVoter;
use App\Document\Sorting\DocumentCategoryGroupStrategy;
use App\Document\Sorting\DocumentNameStrategy;
use App\Framework\Sorting\Sorter;
use App\Common\View\Filter\GradeFilter;
use App\Common\View\Filter\StudyGroupFilter;
use App\Common\View\Filter\UserTypeFilter;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/documents')]
#[IsFeatureEnabled(Feature::Documents)]
class DocumentsController extends AbstractController {

    public function __construct(private Grouper $grouper, private Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);
    }

    #[Route(path: '', name: 'documents')]
    public function index(DocumentRepositoryInterface $documentRepository, GradeFilter $gradeFilter, UserTypeFilter $userTypeFilter,
                          SectionResolverInterface $sectionResolver, Request $request): Response {
        /** @var User $user */
        $user = $this->getUser();

        $q = $request->query->get('q', null);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionResolver->getCurrentSection(), $user);
        $userTypeFilterView = $userTypeFilter->handle($request->query->get('user_type', null), $user, $user->isStudentOrParent(), $user->getUserType());

        $documents = $documentRepository->findAllFor($userTypeFilterView->getCurrentType(), $gradeFilterView->getCurrentGrade(), $q);
        $documents = array_filter($documents, fn(Document $document) => $this->isGranted(DocumentVoter::View, $document));

        $this->sorter->sort($documents, DocumentNameStrategy::class);
        $categories = $this->grouper->group($documents, DocumentCategoryGroupingStrategy::class);
        $this->sorter->sort($categories, DocumentCategoryGroupStrategy::class);

        return $this->render('documents/index.html.twig', [
            'gradeFilter' => $gradeFilterView,
            'userTypeFilter' => $userTypeFilterView,
            'categories' => $categories,
            'q' => $q
        ]);
    }

    #[Route(path: '/attachment/{uuid}', name: 'download_document_attachment')]
    public function downloadAttachment(#[MapEntity(mapping: ['uuid' => 'uuid'])] DocumentAttachment $attachment, DocumentFilesystem $documentFilesystem): Response {
        $this->denyAccessUnlessGranted(DocumentVoter::View, $attachment->getDocument());

        try {
            return $documentFilesystem->getDownloadResponse($attachment);
        } catch (FileNotFoundException) {
            throw new NotFoundHttpException();
        }
    }

    #[Route(path: '/{uuid}', name: 'show_document')]
    public function show(#[MapEntity(mapping: ['uuid' => 'uuid'])] Document $document): Response {
        $this->denyAccessUnlessGranted(DocumentVoter::View, $document);

        return $this->render('documents/show.html.twig', [
            'document' => $document
        ]);
    }
}