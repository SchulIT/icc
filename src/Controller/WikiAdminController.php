<?php

namespace App\Controller;

use App\Entity\WikiArticle;
use App\Form\WikiArticleType;
use App\Repository\LogRepositoryInterface;
use App\Repository\WikiArticleRepositoryInterface;
use App\Security\Voter\WikiVoter;
use App\Sorting\LogEntryStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use EasySlugger\SluggerInterface;
use League\Flysystem\FilesystemInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
     * @Route("/{uuid}/edit", name="edit_wiki_article")
     */
    public function edit(WikiArticle $article, Request $request) {
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

    /**
     * @Route("/{uuid}/versions", name="wiki_article_versions")
     */
    public function versions(WikiArticle $article, Request $request, LogRepositoryInterface $logRepository, Sorter $sorter) {
        $this->denyAccessUnlessGranted(WikiVoter::Edit, $article);

        $logs = $logRepository->getLogEntries($article);
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
     * @Route("/{uuid}/versions/{version}", name="wiki_article_version")
     */
    public function version(WikiArticle $article, Request $request, LogRepositoryInterface $logRepository, int $version) {
        $this->denyAccessUnlessGranted(WikiVoter::Edit, $article);

        $logs = $logRepository->getLogEntries($article);
        $entry = null;

        foreach($logs as $logEntry) {
            if($logEntry->getVersion() === $version) {
                $entry = $logEntry;
            }
        }

        if($entry === null) {
            throw new NotFoundHttpException();
        }

        $logRepository->revert($article, $version);

        return $this->render('admin/wiki/version.html.twig', [
            'article' => $article,
            'entry' => $entry,
            'token_id' => static::RevertCsrfToken,
            'token_param' => static::RevertCsrfTokenParam,
            'version_param' => static::VersionParam
        ]);
    }

    /**
     * @Route("/{uuid}/restore", name="restore_wiki_article_version")
     */
    public function restore(WikiArticle $article, Request $request, LogRepositoryInterface $logRepository, TranslatorInterface $translator) {
        $this->denyAccessUnlessGranted(WikiVoter::Edit, $article);

        if($this->isCsrfTokenValid(static::RevertCsrfToken, $request->request->get(static::RevertCsrfTokenParam)) !== true) {
            $this->addFlash('error', $translator->trans('The CSRF token is invalid. Please try to resubmit the form.', [], 'validators'));

            return $this->redirectToRoute('wiki_article_versions', [
                'uuid' => $article->getUuid()
            ]);
        }

        $logRepository->revert($article, $request->request->get(static::VersionParam));
        $this->repository->persist($article);

        $this->addFlash('success', 'versions.restore.success');

        return $this->redirectToRoute('show_wiki_article', [
            'uuid' => $article->getUuid()
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_wiki_article")
     */
    public function remove(WikiArticle $article, Request $request, TranslatorInterface $translator) {
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

    /**
     * @Route("/upload", name="wiki_upload", methods={"POST"})
     */
    public function upload(Request $request, FilesystemInterface $wikiFilesystem, SluggerInterface $slugger, UrlGeneratorInterface $urlGenerator) {
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

        throw new BadRequestHttpException('Invalid file uploaded.');
    }
}