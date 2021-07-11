<?php

namespace App\Controller;

use App\Entity\ExcuseNote;
use App\Form\ExcuseType;
use App\Repository\ExcuseNoteRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/book/excuses")
 */
class ExcuseNoteController extends AbstractController {

    private $repository;

    public function __construct(ExcuseNoteRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("", name="excuse_notes")
     */
    public function index() {
        return $this->render('excuse_note/index.html.twig', [

        ]);
    }

    /**
     * @Route("/add", name="add_excuse")
     */
    public function add(Request $request) {
        $excuse = new ExcuseNote();
        $form = $this->createForm(ExcuseType::class, $excuse);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($excuse);

            $this->addFlash('success', 'excuse_note.add.success');
            return $this->redirectToRoute('excuse_notes');
        }

        return $this->render('excuse_note/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="edit_excuse")
     */
    public function edit() {

    }

    /**
     * @Route("/{uuid}/remove", name="remove_excuse")
     */
    public function remove() {

    }
}