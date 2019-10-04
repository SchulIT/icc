<?php

namespace App\Controller;

use App\Entity\WikiArticle;
use App\Form\WikiArticleType;
use App\Repository\WikiArticleRepositoryInterface;
use App\Request\BadRequestException;
use App\Utils\RefererHelper;
use EasySlugger\SluggerInterface;
use League\Flysystem\FilesystemInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/admin/wiki")
 * @Security("is_granted('ROLE_WIKI_ADMIN')")
 */
class WikiAdminController extends AbstractController {

    private $repository;

    public function __construct(WikiArticleRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);

        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_wiki")
     */
    public function index() {
        return $this->render('admin/wiki/index.html.twig', [
            'tree' => $this->repository->findAll()
        ]);
    }

    /**
     * @Route("/add", name="add_wiki_article")
     */
    public function add(Request $request) {
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

    /**
     * @Route("/{id}/edit", name="edit_wiki_article")
     */
    public function edit(WikiArticle $article, Request $request) {
        $form = $this->createForm(WikiArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($article);

            return $this->redirectToReferer(['view' => 'show_wiki_article'], 'admin_wiki', [ 'id' => $article->getId(), 'slug' => $article->getSlug() ]);
        }

        return $this->render('admin/wiki/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    /**
     * @Route("/{id}/remove", name="remove_wiki_article")
     */
    public function remove() {

    }

    /**
     * @Route("/upload", name="wiki_upload", methods={"POST"})
     */
    public function upload(Request $request, FilesystemInterface $wikiFilesystem, SluggerInterface $slugger, UrlGeneratorInterface $urlGenerator) {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('file');

        if($file === null) {
            throw new BadRequestException('File is missing.');
        }

        if($file->isValid()) {
            $ext = $file->getClientOriginalExtension();
            $name = substr($file->getClientOriginalName(), 0, -strlen($ext) - 1);

            do {
                $filename = sprintf('%s.%s', $slugger->uniqueSlugify($name), $ext);
            } while($wikiFilesystem->has($filename));

            $stream = fopen($file->getRealPath(), 'r+');
            $wikiFilesystem->writeStream($filename, $stream);
            fclose($stream);

            return $this->json([
                'filename' => $urlGenerator->generate('wiki_image', [
                    'filename' => $filename
                ])
            ]);
        }

        throw new BadRequestException('Invalid file uploaded.');
    }
}