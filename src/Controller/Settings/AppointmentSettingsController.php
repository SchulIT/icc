<?php

namespace App\Controller\Settings;

use App\Converter\EnumStringConverter;
use App\Entity\UserType;
use App\Form\ColorType;
use App\Settings\AppointmentsSettings;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/settings')]
#[IsGranted('ROLE_ADMIN')]
class AppointmentSettingsController extends AbstractController {
    #[Route(path: '/appointments', name: 'admin_settings_appointments')]
    public function appointments(Request $request, AppointmentsSettings $appointmentsSettings, EnumStringConverter $enumStringConverter): Response {
        $builder = $this->createFormBuilder();
        $userTypes = UserType::cases();

        foreach($userTypes as $name => $userType) {
            $builder
                ->add(sprintf('start_%s', $name), DateType::class, [
                    'label' => 'admin.settings.appointments.start.label',
                    'label_translation_parameters' => [
                        '%type%' => $enumStringConverter->convert($userType)
                    ],
                    'help' => 'admin.settings.appointments.start.help',
                    'data' => $appointmentsSettings->getStart($userType),
                    'widget' => 'single_text',
                    'required' => false
                ])
                ->add(sprintf('end_%s', $name), DateType::class, [
                    'label' => 'admin.settings.appointments.end.label',
                    'label_translation_parameters' => [
                        '%type%' => $enumStringConverter->convert($userType)
                    ],
                    'help' => 'admin.settings.appointments.end.help',
                    'data' => $appointmentsSettings->getEnd($userType),
                    'widget' => 'single_text',
                    'required' => false
                ]);
        }

        $builder->add('exam_color', ColorType::class, [
            'label' => 'admin.settings.appointments.exam_color.label',
            'help' => 'admin.settings.appointments.exam_color.help',
            'data' => $appointmentsSettings->getExamColor(),
            'required' => false
        ]);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'exam_color' => function(?string $color) use($appointmentsSettings) {
                    $appointmentsSettings->setExamColor($color);
                }
            ];

            foreach($userTypes as $name => $userType) {
                $map['start_' . $name] = function(?DateTime $dateTime) use ($appointmentsSettings, $userType) {
                    $appointmentsSettings->setStart($userType, $dateTime);
                };

                $map['end_' . $name] = function(?DateTime $dateTime) use ($appointmentsSettings, $userType) {
                    $appointmentsSettings->setEnd($userType, $dateTime);
                };
            }

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_appointments');

        }

        return $this->render('admin/settings/appointments.html.twig', [
            'form' => $form->createView(),
            'userTypes' => $userTypes
        ]);
    }
}