<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
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