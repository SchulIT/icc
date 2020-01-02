<?php

namespace App\Controller;

use App\Entity\WikiArticle;
use App\Form\WikiArticleType;
use App\Repository\WikiArticleRepositoryInterface;
use App\Request\BadRequestException;
use App\Security\Voter\WikiVoter;
use App\Sorting\LogEntryStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use App\Utils\RefererHelper;
use EasySlugger\SluggerInterface;
use Gedmo\Loggable\Entity\LogEntry;
use League\Flysystem\FilesystemInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/wiki")
 * @Security("is_granted('ROLE_WIKI_ADMIN')")
 */
class WikiAdminController extends AbstractController {

    private const VersionParam = '_version';
    private const RevertCsrfTokenParam = '_csrf_token';
    private const RevertCsrfToken = 'revert-wiki-article';

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
     * @Route("/{id}/versions", name="wiki_article_versions")
     */
    public function versions(WikiArticle $article, Request $request, Sorter $sorter) {
        $this->denyAccessUnlessGranted(WikiVoter::Edit, $article);

        $repo = $this->getDoctrine()->getRepository(LogEntry::class);
        $logs = $repo->getLogEntries($article);

        $sorter->sort($logs, LogEntryStrategy::class, SortDirection::Descending());

        return $this->render('admin/wiki/versions.html.twig', [
            'article' => $article,
            'logs' => $logs,
            'token_id' => static::RevertCsrfToken,
            'token_param' => static::RevertCsrfTokenParam,
            'version_param' => static::VersionParam
        ]);
    }

    /**
     * @Route("/{id}/versions/{version}", name="wiki_article_version")
     */
    public function version(WikiArticle $article, Request $request, int $version) {
        $this->denyAccessUnlessGranted(WikiVoter::Edit, $article);

        $repo = $this->getDoctrine()->getRepository(LogEntry::class);
        $logs = $repo->getLogEntries($article);

        $entry = null;

        foreach($logs as $logEntry) {
            if($logEntry->getVersion() === $version) {
                $entry = $logEntry;
            }
        }

        if($entry === null) {
            throw new NotFoundHttpException();
        }

        $repo->revert($article, $version);

        return $this->render('admin/wiki/version.html.twig', [
            'article' => $article,
            'entry' => $entry,
            'token_id' => static::RevertCsrfToken,
            'token_param' => static::RevertCsrfTokenParam,
            'version_param' => static::VersionParam
        ]);
    }

    /**
     * @Route("/{id}/restore", name="restore_wiki_article_version")
     */
    public function restore(WikiArticle $article, Request $request, TranslatorInterface $translator) {
        $this->denyAccessUnlessGranted(WikiVoter::Edit, $article);

        if($this->isCsrfTokenValid(static::RevertCsrfToken, $request->request->get(static::RevertCsrfTokenParam)) !== true) {
            $this->addFlash('error', $translator->trans('The CSRF token is invalid. Please try to resubmit the form.', [], 'validators'));

            return $this->redirectToRoute('wiki_article_versions', [
                'id' => $article->getId()
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(LogEntry::class);

        $repo->revert($article, $request->request->get(static::VersionParam));
        $em->persist($article);
        $em->flush();

        $this->addFlash('success', 'admin.wiki.versions.success');

        return $this->redirectToRoute('show_wiki_article', [
            'id' => $article->getId(),
            'slug' => $article->getSlug()
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