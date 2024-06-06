<?php

namespace App\Controller\NRW;

use App\Book\Grade\Export\XNM\ConfigurationGuesser;
use App\Book\Grade\Export\XNM\ConfigurationType;
use App\Book\Grade\Export\XNM\Exporter;
use App\Controller\AbstractController;
use App\Settings\TuitionGradebookSettings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/book/gradebook/xnm')]
class XNMExportController extends AbstractController {

    #[Route('', name: 'nrw_xnm')]
    public function leistungsdaten(Request $request, Exporter $exporter, ConfigurationGuesser $configurationGuesser, ValidatorInterface $validator, TuitionGradebookSettings $gradebookSettings): Response {
        $configuration = $configurationGuesser->guess();
        $form = $this->createForm(ConfigurationType::class, $configuration, [
            'method' => 'GET',
            'csrf_protection' => false
        ]);
        $form->handleRequest($request);

        $view = null;

        if(($form->isSubmitted() && $form->isValid()) || $validator->validate($configuration)->count() === 0) {
            $view = $exporter->createView($configuration);
        }

        return $this->render('books/grades/export/xnm.html.twig', [
            'form' => $form->createView(),
            'view' => $view,
            'key' => $gradebookSettings->getEncryptedMasterKey(),
            'ttl' => $gradebookSettings->getTtlForSessionStorage(),
        ]);
    }
}