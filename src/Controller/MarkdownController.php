<?php

namespace App\Controller;

use App\Markdown\Markdown;
use EasySlugger\SluggerInterface;
use SchoolIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MarkdownController extends AbstractController {
    private $markdown;
    private $slugger;
    private $baseUrl;

    public function __construct(string $baseUrl, Markdown $markdown, SluggerInterface $slugger, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);

        $this->markdown = $markdown;
        $this->slugger = $slugger;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @Route("/markdown/preview", name="markdown_preview")
     */
    public function preview(Request $request) {
        $body = $request->getContent();

        $html = $this->markdown->convertToHtml($body);
        return new Response($html);
    }

    /**
     * @Route("/markdown/upload", name="markdown_upload")
     */
    public function upload(Request $request) {
        throw new NotFoundHttpException();
    }
}