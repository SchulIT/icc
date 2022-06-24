<?php

namespace App\Controller;

use App\Markdown\Markdown;
use EasySlugger\SluggerInterface;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MarkdownController extends AbstractController {
    private Markdown $markdown;

    public function __construct(Markdown $markdown, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);

        $this->markdown = $markdown;
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