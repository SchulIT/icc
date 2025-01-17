<?php

namespace App\Controller\Settings;

use App\Entity\Section;
use App\Repository\SectionRepositoryInterface;
use App\Settings\GeneralSettings;
use App\Utils\ArrayUtils;
use SchulIT\CommonBundle\Helper\DateHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/settings')]
#[Security("is_granted('ROLE_ADMIN')")]
class GeneralSettingsController extends AbstractController {
    #[Route(path: '/general', name: 'admin_settings_general')]
    public function general(Request $request, GeneralSettings $settings, DateHelper $dateHelper, SectionRepositoryInterface $sectionRepository): Response {
        $currentYear = (int)$dateHelper->getToday()->format('Y');
        $choices = [ ];
        for($year = $currentYear - 1; $year <= $currentYear + 1; $year++) {
            $choices[sprintf('%d/%d', $year, $year+1)] = $year;
        }

        $builder = $this->createFormBuilder();
        $builder
            ->add('current_section', ChoiceType::class,  [
                'choices' => ArrayUtils::createArrayWithKeysAndValues(
                    $sectionRepository->findAll(),
                    fn(Section $section) => $section->getDisplayName(),
                    fn(Section $section) => $section->getId()
                ),
                'data' => $settings->getCurrentSectionId(),
                'label' => 'admin.settings.general.current_section.label',
                'help' => 'admin.settings.general.current_section.help',
                'attr' => [
                    'data-choice' => 'true'
                ]
            ]);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'current_section' => function($sectionId) use ($settings) {
                    $settings->setCurrentSectionId($sectionId);
                }
            ];

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_general');
        }

        return $this->render('admin/settings/general.html.twig', [
            'form' => $form->createView()
        ]);
    }
}