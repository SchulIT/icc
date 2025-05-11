<?php

namespace App\Controller;

use App\Entity\WikiArticle;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Form\WikiArticleType;
use App\Repository\LogRepositoryInterface;
use App\Repository\WikiArticleRepositoryInterface;
use App\Security\Voter\WikiVoter;
use App\Sorting\LogEntryStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use EasySlugger\SluggerInterface;
use League\Flysystem\Filesystem;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/wiki')]
#[IsGranted('ROLE_WIKI_ADMIN')]
#[IsFeatureEnabled(Feature::Wiki)]
class WikiAdminController extends AbstractController {

    public function __construct(private WikiArticleRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route(path: '', name: 'admin_wiki')]
    public function index(): Response {
        return $this->render('admin/wiki/index.html.twig', [
            'tree' => $this->repository->findAll()
        ]);
    }

    #[Route(path: '/add', name: 'add_wiki_article')]
    public function add(Request $request): Response {
        $article = new WikiArticle();
        $form = $this->createForm(WikiArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($article);

            return $this->redirectToRoute('admin_wiki');
        }

        return $this->render('admin/wiki/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_wiki_article')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] WikiArticle $article, Request $request): Response {
        $form = $this->createForm(WikiArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($article);

            return $this->redirectToReferer(['view' => 'show_wiki_article'], 'admin_wiki', [ 'uuid' => $article->getUuid() ]);
        }

        return $this->render('admin/wiki/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_wiki_article')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] WikiArticle $article, Request $request, TranslatorInterface $translator): Response {
        $this->denyAccessUnlessGranted(WikiVoter::Remove, $article);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $translator->trans('admin.wiki.remove.confirm', [
                '%name%' => $article->getTitle()
            ])
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($article);

            $this->addFlash('success', 'admin.wiki.remove.success');

            return $this->redirectToRoute('admin_wiki', [
                'uuid' => $article->getParent() !== null ? $article->getParent()->getUuid() : null
            ]);
        }

        return $this->render('admin/wiki/remove.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    #[Route(path: '/upload', name: 'wiki_upload', methods: ['POST'])]
    public function upload(Request $request, Filesystem $wikiFilesystem, SluggerInterface $slugger, UrlGeneratorInterface $urlGenerator): Response {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('file');

        if($file === null) {
            throw new BadRequestHttpException('File is missing.');
        }

        if($file->isValid()) {
            $ext = $file->getClientOriginalExtension();
            $name = substr($file->getClientOriginalName(), 0, -strlen($ext) - 1);

            do {
                $filename = sprintf('%s.%s', $slugger->uniqueSlugify($name), $ext);
            } while($wikiFilesystem->fileExists($filename));

            $stream = fopen($file->getRealPath(), 'r+');
            $wikiFilesystem->writeStream($filename, $stream);
            fclose($stream);

            return $this->json([
                'filename' => $urlGenerator->generate('wiki_image', [
                    'filename' => $filename
                ])
            ]);
        }

        throw new BadRequestHttpException('Invalid file uploaded.');
    }
}