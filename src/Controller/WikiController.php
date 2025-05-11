<?php

namespace App\Controller;

use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\WikiArticle;
use App\Http\FlysystemFileResponse;
use App\Repository\WikiArticleRepositoryInterface;
use App\Security\Voter\WikiVoter;
use League\Flysystem\Filesystem;
use Mimey\MimeTypes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/wiki')]
#[IsFeatureEnabled(Feature::Wiki)]
class WikiController extends AbstractController {

    private const ResultsPerPage = 20;

    public function __construct(private WikiArticleRepositoryInterface $repository)
    {
    }

    #[Route(path: '', name: 'wiki')]
    public function index(): Response {
        return $this->show(null);
    }

    #[Route(path: '/{uuid}', name: 'show_wiki_article')]
    public function show(#[MapEntity(mapping: ['uuid' => 'uuid'])] ?WikiArticle $article): Response {
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

    #[Route(path: '/image/{filename}', name: 'wiki_image')]
    public function image($filename, Filesystem $wikiFilesystem, MimeTypes $mimeTypes): Response {
        if($wikiFilesystem->fileExists($filename) !== true) {
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

    #[Route(path: '/search', name: 'wiki_search', priority: 10)]
    public function search(Request $request): Response {
        $p = $request->query->getInt('p', 1);
        $q = $request->query->get('q');

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
            $pages = ceil((float)count($results) / self::ResultsPerPage);
        }

        if(!is_numeric($p) || $p <= 0 || $p > $pages) {
            $p = 1;
        }

        $offset = ($p - 1) * self::ResultsPerPage;
        $results = array_slice($results, $offset, self::ResultsPerPage);

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