<?php

namespace App\Controller;

use App\Entity\User;
use App\Grouping\DocumentCategoryStrategy as DocumentCategoryGroupingStrategy;
use App\Grouping\Grouper;
use App\Repository\DocumentRepositoryInterface;
use App\Repository\WikiArticleRepositoryInterface;
use App\Sorting\DocumentCategoryGroupStrategy;
use App\Sorting\DocumentNameStrategy;
use App\Sorting\Sorter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/editor")
 */
class EditorController extends SymfonyAbstractController {

    private Grouper $grouper;
    private Sorter $sorter;

    public function __construct(Grouper $grouper, Sorter $sorter) {
        $this->grouper = $grouper;
        $this->sorter = $sorter;
    }

    /**
     * @Route("/links", name="editor_links")
     */
    public function links(DocumentRepositoryInterface $documentRepository, WikiArticleRepositoryInterface $wikiArticleRepository) {
        /** @var User $user */
        $user = $this->getUser();

        // Documents
        $documents = $documentRepository->findAllFor($user->getUserType());

        $this->sorter->sort($documents, DocumentNameStrategy::class);
        $categories = $this->grouper->group($documents, DocumentCategoryGroupingStrategy::class);
        $this->sorter->sort($categories, DocumentCategoryGroupStrategy::class);

        // Wiki articles
        $articles = $wikiArticleRepository->findAll();

        return $this->render('admin/editor/links.html.twig', [
            'categories' => $categories,
            'tree' => $articles
        ]);
    }
}