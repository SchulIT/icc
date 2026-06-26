<?php

namespace App\Appointment\Settings;

use App\Appointment\External\OpenHolidaysClient\Client;
use App\Appointment\Repository\AppointmentCategoryRepositoryInterface;
use App\Framework\Converter\EnumStringConverter;
use App\Common\Entity\UserType;
use App\Common\Form\Type\ColorType;
use App\Appointment\Settings\AppointmentsSettings;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/settings')]
#[IsGranted('ROLE_ADMIN')]
class AppointmentSettingsController extends AbstractController {

    public const string LANGUAGE = 'DE';


    #[Route(path: '/appointments', name: 'admin_settings_appointments')]
    public function appointments(
        Request $request,
        AppointmentsSettings $appointmentsSettings,
        EnumStringConverter $enumStringConverter,
        Client $client,
        AppointmentCategoryRepositoryInterface $categoryRepository
    ): Response {
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
        ])
            ->add('import_country', ChoiceType::class, [
                'choices' => $this->getCountries($client),
                'label' => 'admin.settings.appointments.import.country.label',
                'help' => 'admin.settings.appointments.import.country.help',
                'data' => $appointmentsSettings->getImportCountry(),
                'required' => false
            ])
            ->add('import_subdivision', ChoiceType::class, [
                'choices' => $this->getSubdivisions($client, $appointmentsSettings->getImportCountry()),
                'label' => 'admin.settings.appointments.import.subdivision.label',
                'help' => 'admin.settings.appointments.import.subdivision.help',
                'data' => $appointmentsSettings->getImportSubdivision(),
                'required' => false
            ])
            ->add('import_category', ChoiceType::class, [
                'choices' => $this->getCategories($categoryRepository),
                'label' => 'admin.settings.appointments.import.category.label',
                'help' => 'admin.settings.appointments.import.category.help',
                'data' => $appointmentsSettings->getImportAppointmentCategoryId(),
                'required' => false
            ]);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'exam_color' => function(?string $color) use($appointmentsSettings) {
                    $appointmentsSettings->setExamColor($color);
                },
                'import_country' => function(?string $country) use($appointmentsSettings) {
                    $appointmentsSettings->setImportCountry($country);
                },
                'import_subdivision' => function(?string $subdivision) use($appointmentsSettings) {
                    $appointmentsSettings->setImportSubdivision($subdivision);
                },
                'import_category' => function(int $category) use($appointmentsSettings) {
                    $appointmentsSettings->setImportAppointmentCategoryId($category);
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

    private function getCountries(Client $client): array {
        $choices = [ ];

        foreach($client->countries(self::LANGUAGE) as $country) {
            $name = array_first($country->name);
            $choices[$name->text] = $country->isoCode;
        }

        return $choices;
    }

    private function getSubdivisions(Client $client, string|null $country): array {
        if($country === null) {
            return [ ];
        }

        $choices = [ ];

        foreach($client->subdivisions($country, self::LANGUAGE) as $subdivision) {
            $name = array_first($subdivision->name);
            $choices[$name->text] = $subdivision->isoCode;
        }

        return $choices;
    }

    private function getCategories(AppointmentCategoryRepositoryInterface $repository): array {
        $choices = [ ];

        foreach($repository->findAll() as $category)
            $choices[$category->getName()] = $category->getId();

        return $choices;
    }
}
