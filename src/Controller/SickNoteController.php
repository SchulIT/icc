<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserType;
use App\Form\SickNoteType;
use App\Repository\SickNoteRepositoryInterface;
use App\Security\Voter\SickNoteVoter;
use App\Settings\SickNoteSettings;
use App\SickNote\SickNote;
use App\SickNote\SickNoteSender;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SickNoteController extends AbstractController {

    /**
     * @Route("/sick_note", name="sick_note")
     */
    public function add(Request $request, SickNoteSender $sender, SickNoteSettings $settings, SickNoteRepositoryInterface $repository) {
        $this->denyAccessUnlessGranted(SickNoteVoter::New);

        if($settings->isEnabled() !== true) {
            throw new NotFoundHttpException();
        }

        /** @var User $user */
        $user = $this->getUser();
        $students = $user->getStudents()->toArray();

        if($user->getUserType()->equals(UserType::Student())) {
            $students = [ array_shift($students) ];
        }

        $note = new SickNote();
        $form = $this->createForm(SickNoteType::class, $note, [
            'students' => $students
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $sender->sendSickNote($note, $user);

            $this->addFlash('success', 'sick_note.success');
            return $this->redirectToRoute('sick_note');
        }

        return $this->render('sick_note/index.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
            'sick_notes' => $repository->findByUser($user)
        ]);
    }
}