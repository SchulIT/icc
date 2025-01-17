<?php

namespace App\Controller;

use App\Markdown\Markdown;
use EasySlugger\SluggerInterface;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class MarkdownController extends AbstractController {
    public function __construct(private Markdown $markdown, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);
    }

    #[Route(path: '/markdown/preview', name: 'markdown_preview')]
    public function preview(Request $request): Response {
        $body = $request->getContent();

        $html = $this->markdown->convertToHtml($body);
        return new Response($html);
    }

    #[Route(path: '/markdown/upload', name: 'markdown_upload')]
    public function upload(): Response
    {
        throw new NotFoundHttpException();
    }
}