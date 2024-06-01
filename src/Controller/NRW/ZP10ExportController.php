<?php

namespace App\Controller\NRW;

use App\Book\Grade\Export\ZP10\ConfigurationGuesser;
use App\Book\Grade\Export\ZP10\ConfigurationType;
use App\Book\Grade\Export\ZP10\Exporter;
use App\Settings\TuitionGradebookSettings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
#[Route('/book/gradebook/zp10')]
class ZP10ExportController extends AbstractController {
    #[Route('', name: 'nrw_zp10')]
    public function zp10(Request $request, Exporter $exporter, ConfigurationGuesser $configurationGuesser, ValidatorInterface $validator, TuitionGradebookSettings $gradebookSettings): Response {
         $configuration = $configurationGuesser->guess();
        $form = $this->createForm(ConfigurationType::class, $configuration, [
            'method' => 'GET'
        ]);
        $form->handleRequest($request);

        $view = null;

        if(($form->isSubmitted() && $form->isValid()) || $validator->validate($configuration)->count() === 0 ) {
            $view = $exporter->createView($configuration);
        }

        return $this->render('books/grades/export/zp10.html.twig', [
            'form' => $form->createView(),
            'view' => $view,
            'key' => $gradebookSettings->getEncryptedMasterKey(),
            'ttl' => $gradebookSettings->getTtlForSessionStorage(),
        ]);
    }
}