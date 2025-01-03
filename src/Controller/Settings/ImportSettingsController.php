<?php

namespace App\Controller\Settings;

use App\Entity\Section;
use App\Form\ExamStudentRuleType;
use App\Form\TextCollectionEntryType;
use App\Repository\SectionRepositoryInterface;
use App\Settings\ImportSettings;
use App\Utils\ArrayUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Route(path: '/admin/settings')]
#[Security("is_granted('ROLE_ADMIN')")]
class ImportSettingsController extends AbstractController {
    #[Route(path: '/import', name: 'admin_settings_import')]
    public function import(Request $request, ImportSettings $settings, SectionRepositoryInterface $sectionRepository): Response {
        $builder = $this->createFormBuilder();
        $builder
            ->add('rules', CollectionType::class, [
                'entry_type' => ExamStudentRuleType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'help' => 'label.comma_separated',
                'data' => $settings->getExamRules()
            ])
            ->add('subjects_without_tuition', CollectionType::class, [
                'entry_type' => TextCollectionEntryType::class,
                'entry_options' => [
                    'constraints' => [ new NotBlank() ]
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'data' => $settings->getSubjectsWithoutTuition()
            ])
            ->add('fallback_section', ChoiceType::class, [
                'label' => 'label.section',
                'placeholder' => 'label.choose',
                'choices' => ArrayUtils::createArrayWithKeysAndValues(
                    $sectionRepository->findAll(),
                    fn(Section $section) => $section->getDisplayName(),
                    fn(Section $section) => $section->getId()
                ),
                'attr' => [
                    'data-choice' => 'true'
                ],
                'data' => $settings->getFallbackSection()
            ]);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'rules' => function(array $rules) use($settings) {
                    $settings->setExamRules($rules);
                },
                'subjects_without_tuition' => function(array $subjects) use ($settings) {
                    $settings->setSubjectsWithoutTuition($subjects);
                },
                'fallback_section' => function(?int $sectionId) use($settings) {
                    $settings->setFallbackSection($sectionId);
                }
            ];

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_import');
        }

        return $this->render('admin/settings/import.html.twig', [
            'form' => $form->createView()
        ]);
    }
}