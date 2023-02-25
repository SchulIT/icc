<?php

namespace App\Controller\Settings;

use App\Converter\EnumStringConverter;
use App\Entity\AppointmentCategory;
use App\Entity\Grade;
use App\Entity\UserType;
use App\Form\ColorType;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Repository\GradeRepositoryInterface;
use App\Settings\TimetableSettings;
use App\Utils\ArrayUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/settings')]
#[Security("is_granted('ROLE_ADMIN')")]
class TimetableSettingsController extends AbstractController {
    #[Route(path: '/timetable', name: 'admin_settings_timetable')]
    public function timetable(Request $request, TimetableSettings $timetableSettings, GradeRepositoryInterface $gradeRepository,
                              AppointmentCategoryRepositoryInterface $appointmentCategoryRepository, EnumStringConverter $enumStringConverter, TranslatorInterface $translator): Response {
        $builder = $this->createFormBuilder();
        $builder
            ->add('days', ChoiceType::class, [
                'label' => 'admin.settings.timetable.days.label',
                'help' => 'admin.settings.timetable.days.help',
                'data' => $timetableSettings->getDays(),
                'choices' => [
                    'date.days.0' => 0,
                    'date.days.1' => 1,
                    'date.days.2' => 2,
                    'date.days.3' => 3,
                    'date.days.4' => 4,
                    'date.days.5' => 5,
                    'date.days.6' => 6
                ],
                'expanded' => true,
                'multiple' => true,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('lessons', IntegerType::class, [
                'label' => 'admin.settings.timetable.max_lessons.label',
                'help' => 'admin.settings.timetable.max_lessons.help',
                'constraints' => [
                    new Type(['type' => 'integer']),
                    new GreaterThanOrEqual(['value' => 0])
                ],
                'data' => $timetableSettings->getMaxLessons()
            ])
            ->add('categories', ChoiceType::class, [
                'label' => 'admin.settings.timetable.no_school_category.label',
                'help' => 'admin.settings.timetable.no_school_category.help',
                'choices' => ArrayUtils::createArrayWithKeysAndValues($appointmentCategoryRepository->findAll(), fn(AppointmentCategory $category) => $category->getName(), fn(AppointmentCategory $category) => $category->getId()),
                'placeholder' => 'admin.settings.timetable.no_school_category.none',
                'required' => false,
                'multiple' => true,
                'data' => $timetableSettings->getCategoryIds(),
                'expanded' => true,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('grades_course_names', ChoiceType::class, [
                'label' => 'admin.settings.timetable.grades_course_names.label',
                'help' => 'admin.settings.timetable.grades_course_names.help',
                'choices' => ArrayUtils::createArrayWithKeysAndValues($gradeRepository->findAll(), fn(Grade $grade) => $grade->getName(), fn(Grade $grade) => $grade->getId()),
                'multiple' => true,
                'attr' => [
                    'data-choice' => 'true'
                ],
                'data' => $timetableSettings->getGradeIdsWithCourseNames()
            ])
            ->add('grades_membership_types', ChoiceType::class, [
                'label' => 'admin.settings.timetable.grades_membership_types.label',
                'help' => 'admin.settings.timetable.grades_membership_types.help',
                'choices' => ArrayUtils::createArrayWithKeysAndValues($gradeRepository->findAll(), fn(Grade $grade) => $grade->getName(), fn(Grade $grade) => $grade->getId()),
                'multiple' => true,
                'attr' => [
                    'data-choice' => 'true'
                ],
                'data' => $timetableSettings->getGradeIdsWithMembershipTypes()
            ]);

        $userTypes = UserType::cases();

        foreach($userTypes as $name => $userType) {
            $builder
                ->add(sprintf('start_%s', $name), DateType::class, [
                    'label' => 'admin.settings.appointments.start.label',
                    'label_translation_parameters' => [
                        '%type%' => $enumStringConverter->convert($userType)
                    ],
                    'help' => 'admin.settings.appointments.start.help',
                    'data' => $timetableSettings->getStartDate($userType),
                    'widget' => 'single_text',
                    'required' => false
                ])
                ->add(sprintf('end_%s', $name), DateType::class, [
                    'label' => 'admin.settings.appointments.end.label',
                    'label_translation_parameters' => [
                        '%type%' => $enumStringConverter->convert($userType)
                    ],
                    'help' => 'admin.settings.appointments.end.help',
                    'data' => $timetableSettings->getEndDate($userType),
                    'widget' => 'single_text',
                    'required' => false
                ]);
        }

        for($lesson = 1; $lesson <= $timetableSettings->getMaxLessons(); $lesson++) {
            $builder
                ->add(sprintf('lesson_%d_start', $lesson), TimeType::class, [
                    'label' => $translator->trans('admin.settings.timetable.lesson.start', [ '%lesson%' => $lesson ]),
                    'data' => $timetableSettings->getStart($lesson),
                    'widget' => 'single_text',
                    'required' => false,
                    'input' => 'string',
                    'input_format' => 'H:i'
                ])
                ->add(sprintf('lesson_%d_end', $lesson), TimeType::class, [
                    'label' => $translator->trans('admin.settings.timetable.lesson.end', [ '%lesson%' => $lesson ]),
                    'data' => $timetableSettings->getEnd($lesson),
                    'widget' => 'single_text',
                    'required' => false,
                    'input' => 'string',
                    'input_format' => 'H:i'
                ]);

            if($lesson > 1) {
                $builder
                    ->add(sprintf('lesson_%d_collapsible', $lesson), CheckboxType::class, [
                        'label' => $translator->trans('admin.settings.timetable.lesson.collapsible', ['%lesson%' => $lesson]),
                        'data' => $timetableSettings->isCollapsible($lesson),
                        'required' => false,
                        'label_attr' => [
                            'class' => 'checkbox-custom'
                        ]
                    ]);
            }
        }

        $builder
            ->add('supervision_label', TextType::class, [
                'label' => 'admin.settings.timetable.supervision.label',
                'required' => true,
                'data' => $timetableSettings->getSupervisionLabel(),
            ])
            ->add('supervision_begin', TimeType::class, [
                'label' => 'admin.settings.timetable.supervision.start',
                'data' => $timetableSettings->getStart(0),
                'widget' => 'single_text',
                'input' => 'string'
            ])
            ->add('supervision_color', ColorType::class, [
                'label' => 'admin.settings.timetable.supervision.color',
                'data' => $timetableSettings->getSupervisionColor(),
                'required' => false
            ]);

        for($lesson = 1; $lesson <= $timetableSettings->getMaxLessons(); $lesson++) {
            $builder
                ->add(sprintf('supervision_label_before_%d', $lesson), TextType::class, [
                    'label' => $translator->trans('admin.settings.timetable.supervision.label_before.label', [ '%lesson%' => $lesson ]),
                    'help' => $translator->trans('admin.settings.timetable.supervision.label_before.help', [ '%lesson%' => $lesson ]),
                    'data' => $timetableSettings->getDescriptionBeforeLesson($lesson),
                    'required' => false
                ]);
        }

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $timetableSettings->setMaxLessons($form->get('lessons')->getData());
            $timetableSettings->setCategoryIds($form->get('categories')->getData());
            $timetableSettings->setSupervisionLabel($form->get('supervision_label')->getData());
            $timetableSettings->setSupervisionColor($form->get('supervision_color')->getData());
            $timetableSettings->setStart(0, $form->get('supervision_begin')->getData());
            $timetableSettings->setGradeIdsWithCourseNames($form->get('grades_course_names')->getData());
            $timetableSettings->setGradeIdsWithMembershipTypes($form->get('grades_membership_types')->getData());

            foreach($userTypes as $name => $userType) {
                $timetableSettings->setStartDate($userType, $form->get(sprintf('start_%s', $name))->getData());
                $timetableSettings->setEndDate($userType, $form->get(sprintf('end_%s', $name))->getData());
            }

            for($lesson = 1; $lesson <= $timetableSettings->getMaxLessons(); $lesson++) {
                $startKey = sprintf('lesson_%d_start', $lesson);
                $endKey = sprintf('lesson_%d_end', $lesson);
                $collapsibleKey = sprintf('lesson_%d_collapsible', $lesson);
                $supervisionKey = sprintf('supervision_label_before_%d', $lesson);

                if($form->has($startKey)) {
                    $timetableSettings->setStart($lesson, $form->get($startKey)->getData());
                }

                if($form->has($endKey)) {
                    $timetableSettings->setEnd($lesson, $form->get($endKey)->getData());
                }

                if($form->has($collapsibleKey)) {
                    $timetableSettings->setCollapsible($lesson, $form->get($collapsibleKey)->getData());
                }

                if($form->has($supervisionKey)) {
                    $timetableSettings->setDescriptionBeforeLesson($lesson, $form->get($supervisionKey)->getData());
                }
            }

            $this->addFlash('success', 'admin.settings.success');
            return $this->redirectToRoute('admin_settings_timetable');
        }

        return $this->render('admin/settings/timetable.html.twig', [
            'form' => $form->createView(),
            'maxLessons' => $timetableSettings->getMaxLessons(),
            'userTypes' => $userTypes
        ]);
    }
}