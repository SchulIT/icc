<?php

namespace App\Controller;

use App\Entity\ChatTag;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Form\ChatTagType;
use App\Repository\ChatTagRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/chat/tags')]
#[IsFeatureEnabled(Feature::Chat)]
class ChatTagsAdminController extends AbstractController {

    public function __construct(private readonly ChatTagRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'admin_chat_tags')]
    public function index(): Response {
        return $this->render('admin/chats/tags/index.html.twig', [
            'tags' => $this->repository->findAll()
        ]);
    }

    #[Route('/add', name: 'add_chat_tag')]
    public function add(Request $request): Response {
        $tag = new ChatTag();
        $form = $this->createForm(ChatTagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($tag);
            $this->addFlash('success', 'admin.chat.tags.add.success');
            return $this->redirectToRoute('admin_chat_tags');
        }

        return $this->render('admin/chats/tags/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_chat_tag')]
    public function edit(ChatTag $tag, Request $request): Response {
        $form = $this->createForm(ChatTagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($tag);
            $this->addFlash('success', 'admin.chat.tags.edit.success');
            return $this->redirectToRoute('admin_chat_tags');
        }

        return $this->render('admin/chats/tags/edit.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_chat_tag')]
    public function remove(ChatTag $tag, Request $request, TranslatorInterface $translator): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $translator->trans('admin.chat.tags.remove.confirm', [
                '%title%' => $tag->getName()
            ])
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($tag);
            $this->addFlash('success', 'admin.chat.tags.remove.success');

            return $this->redirectToRoute('admin_chat_tags');
        }

        return $this->render('admin/chats/tags/remove.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag
        ]);
    }
}