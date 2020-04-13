<?php

namespace App\Controller;

use App\Entity\WikiArticle;
use App\Http\FlysystemFileResponse;
use App\Repository\WikiArticleRepositoryInterface;
use App\Security\Voter\WikiVoter;
use League\Flysystem\FilesystemInterface;
use Mimey\MimeTypes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wiki")
 */
class WikiController extends AbstractController {

    private const ResultsPerPage = 20;

    private $repository;

    public function __construct(WikiArticleRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("", name="wiki")
     */
    public function index() {
        return $this->show(null);
    }

    /**
     * @Route("/{uuid}", name="show_wiki_article")
     */
    public function show(?WikiArticle $article) {
        if($article !== null) {
            $this->denyAccessUnlessGranted(WikiVoter::View, $article);
        }

        /** @var WikiArticle[] $children */
        $children = $article !== null ? $article->getChildren() : $this->repository->findAll();

        $childrenWithChildren = [ ];
        $childrenWithoutChildren = [ ];

        foreach($children as $child) {
            if($this->isGranted(WikiVoter::View, $child)) {
                if ($child->getChildren()->count() > 0) {
                    $childrenWithChildren[] = $child;
                } else {
                    $childrenWithoutChildren[] = $child;
                }
            }
        }

        return $this->render('wiki/show.html.twig', [
            'article' => $article,
            'children' => $children,
            'childrenWithoutChildren' => $childrenWithoutChildren,
            'childrenWithChildren' => $childrenWithChildren,
            'visibilities' => $this->getVisibilities($article)
        ]);
    }

    /**
     * @Route("/image/{filename}", name="wiki_image")
     */
    public function image($filename, FilesystemInterface $wikiFilesystem, MimeTypes $mimeTypes) {
        if($wikiFilesystem->has($filename) !== true) {
            throw new NotFoundHttpException();
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        return new FlysystemFileResponse(
            $wikiFilesystem,
            $filename,
            $filename,
            $mimeTypes->getMimeType($extension),
            ResponseHeaderBag::DISPOSITION_INLINE
        );
    }

    /**
     * @Route("/search", name="wiki_search")
     */
    public function search(?string $q, ?int $p = 1) {
        $results = [ ];

        if(!empty($q)) {
            $articles = $this->repository->findAllByQuery($q);

            foreach($articles as $article) {
                if($this->isGranted(WikiVoter::View, $article)) {
                    $results[] = $article;
                }
            }
        }

        // Pagination
        $pages = 0;

        if(count($results) > 0) {
            $pages = ceil((float)count($results) / static::ResultsPerPage);
        }

        if($p === null || !is_numeric($p) || $p <= 0 || $p > $pages) {
            $p = 1;
        }

        $offset = ($p - 1) * static::ResultsPerPage;
        $results = array_slice($results, $offset, static::ResultsPerPage);

        return $this->render('wiki/search.html.twig', [
            'articles' => $results,
            'page' => $p,
            'pages' => $pages,
            'q' => $q
        ]);
    }

    private function getVisibilities(?WikiArticle $article) {
        $visibilities = [ ];

        while($article !== null && count($visibilities) === 0) {
            $visibilities = $article->getVisibilities();
            $article = $article->getParent();
        }

        return $visibilities;
    }
}