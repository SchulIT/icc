<?php

namespace App\Common\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Common\Entity\User;
use App\Document\Grouping\DocumentCategoryStrategy as DocumentCategoryGroupingStrategy;
use App\Framework\Grouping\Grouper;
use App\Document\Repository\DocumentRepositoryInterface;
use App\Wiki\Repository\WikiArticleRepositoryInterface;
use App\Document\Sorting\DocumentCategoryGroupStrategy;
use App\Document\Sorting\DocumentNameStrategy;
use App\Framework\Sorting\Sorter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/editor')]
class EditorController extends SymfonyAbstractController {

    public function __construct(private Grouper $grouper, private Sorter $sorter)
    {
    }

    #[Route(path: '/links', name: 'editor_links')]
    public function links(DocumentRepositoryInterface $documentRepository, WikiArticleRepositoryInterface $wikiArticleRepository): Response {
        // Documents
        $documents = $documentRepository->findAll();

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