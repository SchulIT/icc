<?php

namespace App\Controller;

use App\Converter\EnumStringConverter;
use App\Entity\AppointmentCategory;
use App\Entity\UserType;
use App\Menu\Builder;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Settings\AbstractSettings;
use App\Settings\AppointmentsSettings;
use App\Settings\ExamSettings;
use App\Settings\SubstitutionSettings;
use App\Settings\TimetableSettings;
use App\Utils\ArrayUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/settings")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class SettingsController extends AbstractController {

    /**
     * @Route("", name="admin_settings")
     */
    public function index(Builder $menuBuilder) {
        $settingsMenu = $menuBuilder->settingsMenu([]);

        return $this->render('admin/settings/index.html.twig', [
            'menu' => $settingsMenu->getChildren()
        ]);
    }

    /**
     * @Route("/exams", name="admin_settings_exams")
     */
    public function exams(Request $request, ExamSettings $examSettings, EnumStringConverter $enumStringConverter) {
        $builder = $this->createFormBuilder();
        $builder
            ->add('visibility', ChoiceType::class, [
                'choices' => ArrayUtils::createArray(UserType::keys(), UserType::values()),
                'choice_label' => function(UserType $userType) use($enumStringConverter) {
                    return $enumStringConverter->convert($userType);
                },
                'choice_value' => function(UserType $userType) {
                    return $userType->getValue();
                },
                'expanded' => true,
                'multiple' => true,
                'label' => 'label.visibility',
                'data' => $examSettings->getVisibility()
            ])
            ->add('window', IntegerType::class, [
                'label' => 'admin.settings.exams.window.label',
                'help' => 'admin.settings.exams.window.help',
                'constraints' => [
                    new Type(['type' => 'integer']),
                    new GreaterThanOrEqual(['value' => 0])
                ],
                'data' => $examSettings->getTimeWindowForStudents()
            ])
            ->add('window_invigilators', IntegerType::class, [
                'label' => 'admin.settings.exams.window.invigilators.label',
                'help' => 'admin.settings.exams.window.invigilators.help',
                'constraints' => [
                    new Type(['type' => 'integer']),
                    new GreaterThanOrEqual(['value' => 0])
                ],
                'data' => $examSettings->getTimeWindowForStudentsToSeeInvigilators()
            ])
            ->add('notifications_enabled', CheckboxType::class, [
                'label' => 'admin.settings.exams.notifications.enabled.label',
                'help' => 'admin.settings.exams.notifications.enabled.help',
                'required' => false,
                'data' => $examSettings->isNotificationsEnabled()
            ])
            ->add('notifications_replyaddress', EmailType::class, [
                'label' => 'admin.settings.exams.notifications.reply_address.label',
                'help' => 'admin.settings.exams.notifications.reply_address.help',
                'required' => false,
                'data' => $examSettings->getNotificationReplyToAddress()
            ])
            ->add('number_of_exams_week', IntegerType::class, [
                'label' => 'admin.settings.exams.planning.number_of_exams_week.label',
                'help' => 'admin.settings.exams.planning.number_of_exams_week.help',
                'required' => true,
                'data' => $examSettings->getMaximumNumberOfExamsPerWeek()
            ])
            ->add('number_of_exams_day', IntegerType::class, [
                'label' => 'admin.settings.exams.planning.number_of_exams_day.label',
                'help' => 'admin.settings.exams.planning.number_of_exams_day.help',
                'required' => true,
                'data' => $examSettings->getMaximumNumberOfExamsPerDay()
            ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'visibility' => function(array $visibility) use ($examSettings) {
                    $examSettings->setVisibility($visibility);
                },
                'window' => function(int $window) use ($examSettings) {
                    $examSettings->setTimeWindowForStudents($window);
                },
                'window_invigilators' => function(int $window) use ($examSettings) {
                    $examSettings->setTimeWindowForStudentsToSeeInvigilators($window);
                },
                'notifications_enabled' => function(bool $enabled) use ($examSettings) {
                    $examSettings->setNotificationsEnabled($enabled);
                },
                'notifications_replyaddress' => function(?string $address) use($examSettings) {
                    $examSettings->setNotificationReplyToAddress($address);
                },
                'number_of_exams_week' => function(int $number) use ($examSettings) {
                    $examSettings->setMaximumNumberOfExamsPerWeek($number);
                },
                'number_of_exams_day' => function(int $number) use ($examSettings) {
                    $examSettings->setMaximumNumberOfExamsPerDay($number);
                }
            ];

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_exams');
        }

        return $this->render('admin/settings/exams.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/timetable", name="admin_settings_timetable")
     */
    public function timetable(Request $request, TimetableSettings $timetableSettings,
                              AppointmentCategoryRepositoryInterface $appointmentCategoryRepository, TranslatorInterface $translator) {
        $builder = $this->createFormBuilder();
        $builder
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
                'choices' => $appointmentCategoryRepository->findAll(),
                'placeholder' => 'admin.settings.timetable.no_school_category.none',
                'required' => false,
                'multiple' => true,
                'choice_value' => function(AppointmentCategory $category) {
                    return $category->getId();
                },
                'choice_label' => function(AppointmentCategory $category) {
                    return $category->getName();
                },
                'data' => $timetableSettings->getCategoryIds()
            ]);

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
                        'required' => false
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
            $timetableSettings->setStart(0, $form->get('supervision_begin')->getData());

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
            'maxLessons' => $timetableSettings->getMaxLessons()
        ]);
    }

    /**
     * @Route("/substitutions", name="admin_settings_substitutions")
     */
    public function substitutions(Request $request, SubstitutionSettings $substitutionSettings) {
        $builder = $this->createFormBuilder();
        $builder
            ->add('ahead_days', IntegerType::class, [
                'label' => 'admin.settings.substitutions.number_of_ahead_substitutions.label',
                'help' => 'admin.settings.substitutions.number_of_ahead_substitutions.help',
                'constraints' => [
                    new Type(['type' => 'integer']),
                    new GreaterThanOrEqual(['value' => 0])
                ],
                'data' => $substitutionSettings->getNumberOfAheadDaysForSubstitutions()
            ])
            ->add('skip_weekends', CheckboxType::class, [
                'label' => 'admin.settings.substitutions.skip_weekends.label',
                'help' => 'admin.settings.substitutions.skip_weekends.help',
                'required' => false,
                'data' => $substitutionSettings->skipWeekends()
            ])
            ->add('notifications_enabled', CheckboxType::class, [
                'label' => 'admin.settings.exams.notifications.enabled.label',
                'help' => 'admin.settings.exams.notifications.enabled.help',
                'required' => false,
                'data' => $substitutionSettings->isNotificationsEnabled()
            ])
            ->add('notifications_replyaddress', EmailType::class, [
                'label' => 'admin.settings.exams.notifications.reply_address.label',
                'help' => 'admin.settings.exams.notifications.reply_address.help',
                'required' => false,
                'data' => $substitutionSettings->getNotificationReplyToAddress()
            ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'ahead_days' => function(int $days) use ($substitutionSettings) {
                    $substitutionSettings->setNumberOfAheadDaysForSubstitutions($days);
                },
                'skip_weekends' => function(bool $skipWeekends) use ($substitutionSettings) {
                    $substitutionSettings->setSkipWeekends($skipWeekends);
                },
                'notifications_enabled' => function(bool $enabled) use ($substitutionSettings) {
                    $substitutionSettings->setNotificationsEnabled($enabled);
                },
                'notifications_replyaddress' => function(?string $address) use($substitutionSettings) {
                    $substitutionSettings->setNotificationReplyToAddress($address);
                }
            ];

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_substitutions');
        }

        return $this->render('admin/settings/substitutions.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function appointments(Request $request, AppointmentsSettings $appointmentsSettings) {
        $builder = $this->createFormBuilder();


    }
}