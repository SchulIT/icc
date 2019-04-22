<?php

namespace App\Controller;

use App\Converter\UserTypeStringConverter;
use App\Entity\MessageScope;
use App\Entity\UserType;
use App\Settings\SettingsManager;
use App\Utils\ArrayUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @Route("/admin/settings")
 */
class SettingsController extends AbstractController {

    private $settingsManager;

    public function __construct(SettingsManager $settingsManager) {
        $this->settingsManager = $settingsManager;
    }

    /**
     * @Route("/exams", name="admin_settings_exams")
     */
    public function exams(Request $request, UserTypeStringConverter $typeStringConverter) {
        $builder = $this->createFormBuilder();
        $builder
            ->add('visibility', ChoiceType::class, [
                'choices' => ArrayUtils::createArray(UserType::keys(), UserType::values()),
                'choice_label' => function(UserType $userType) use($typeStringConverter) {
                    return $typeStringConverter->convert($userType);
                },
                'choice_value' => function(UserType $userType) {
                    return $userType->getValue();
                },
                'expanded' => true,
                'multiple' => true,
                'label' => 'label.visibility',
                'data' => $this->settingsManager->getValue('exams.visibility')
            ])
            ->add('window', IntegerType::class, [
                'label' => 'admin.settings.exams.window.label',
                'help' => 'admin.settings.exams.window.help',
                'constraints' => [
                    new Type(['type' => 'integer']),
                    new GreaterThanOrEqual(['value' => 0])
                ],
                'data' => $this->settingsManager->getValue('exams.window', 0)
            ])
            ->add('window-invigilators', IntegerType::class, [
                'label' => 'admin.settings.exams.window.invigilators.label',
                'help' => 'admin.settings.exams.window.invigilators.help',
                'constraints' => [
                    new Type(['type' => 'integer']),
                    new GreaterThanOrEqual(['value' => 0])
                ],
                'data' => $this->settingsManager->getValue('exams.window.invigilators', 0)
            ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'visibility' => 'exams.visibility',
                'window' => 'exams.window',
                'window-invigilators' => 'exams.window.invigilators'
            ];

            foreach($map as $formKey => $settingsKey) {
                $value = $form->get($formKey)->getData();
                $this->settingsManager->setValue($settingsKey, $value);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_exams');
        }

        return $this->render('admin/settings/exams.html.twig', [
            'form' => $form->createView()
        ]);
    }
}