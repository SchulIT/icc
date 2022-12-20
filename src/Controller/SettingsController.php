<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Converter\EnumStringConverter;
use App\Entity\AppointmentCategory;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\UserType;
use App\Form\ColorType;
use App\Form\ExamStudentRuleType;
use App\Form\MarkdownType;
use App\Menu\Builder;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Repository\GradeRepositoryInterface;
use App\Repository\SectionRepositoryInterface;
use App\Settings\AppointmentsSettings;
use App\Settings\BookSettings;
use App\Settings\DashboardSettings;
use App\Settings\ExamSettings;
use App\Settings\GeneralSettings;
use App\Settings\ImportSettings;
use App\Settings\NotificationSettings;
use App\Settings\StudentAbsenceSettings;
use App\Settings\SubstitutionSettings;
use App\Settings\TimetableSettings;
use App\Sorting\GradeNameStrategy;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/settings')]
#[Security("is_granted('ROLE_ADMIN')")]
class SettingsController extends AbstractController {

    #[Route(path: '', name: 'admin_settings')]
    public function index(): Response {
        return $this->redirectToRoute('admin_settings_general');
    }

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
                'help' => 'admin.settings.general.current_section.help'
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

    #[Route(path: '/dashboard', name: 'admin_settings_dashboard')]
    public function dashboard(Request $request, DashboardSettings $dashboardSettings): Response {
        $builder = $this->createFormBuilder();
        $builder
            ->add('removable_types', TextType::class, [
                'label' => 'admin.settings.dashboard.removable_substitutions.label',
                'help' => 'admin.settings.dashboard.removable_substitutions.help',
                'data' => implode(',', $dashboardSettings->getRemovableSubstitutionTypes()),
                'required' => false
            ])
            ->add('additional_types', TextType::class, [
                'label' => 'admin.settings.dashboard.additional_substitutions.label',
                'help' => 'admin.settings.dashboard.additional_substitutions.help',
                'data' => implode(',', $dashboardSettings->getAdditionalSubstitutionTypes()),
                'required' => false
            ])
            ->add('free_lesson_types', TextType::class, [
                'label' => 'admin.settings.dashboard.free_lesson_types.label',
                'help' => 'admin.settings.dashboard.free_lesson_types.help',
                'data' => implode(',', $dashboardSettings->getFreeLessonSubstitutionTypes()),
                'required' => false
            ])
            ->add('next_day_threshold', TimeType::class, [
                'label' => 'admin.settings.dashboard.next_day_threshold.label',
                'help' => 'admin.settings.dashboard.next_day_threshold.help',
                'data' => $dashboardSettings->getNextDayThresholdTime(),
                'required' => false,
                'input' => 'string',
                'input_format' => 'H:i',
                'widget' => 'single_text'
            ])
            ->add('skip_weekends', CheckboxType::class, [
                'label' => 'admin.settings.dashboard.skip_weekends.label',
                'help' => 'admin.settings.dashboard.skip_weekends.help',
                'required' => false,
                'data' => $dashboardSettings->skipWeekends(),
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('past_days', IntegerType::class, [
                'label' => 'admin.settings.dashboard.past_days.label',
                'help' => 'admin.settings.dashboard.past_days.label',
                'required' => true,
                'data' => $dashboardSettings->getNumberPastDays(),
                'constraints' => [
                    new GreaterThanOrEqual(0)
                ]
            ])
            ->add('future_days', IntegerType::class, [
                'label' => 'admin.settings.dashboard.future_days.label',
                'help' => 'admin.settings.dashboard.future_days.label',
                'required' => true,
                'data' => $dashboardSettings->getNumberFutureDays(),
                'constraints' => [
                    new GreaterThanOrEqual(0)
                ]
            ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'removable_types' => function($types) use ($dashboardSettings) {
                    $dashboardSettings->setRemovableSubstitutionTypes(explode(',', $types));
                },
                'additional_types' => function($types) use ($dashboardSettings) {
                    $dashboardSettings->setAdditionalSubstitutionTypes(explode(',', $types));
                },
                'free_lesson_types' => function($types) use ($dashboardSettings) {
                    $dashboardSettings->setFreeLessonSubstitutionTypes(explode(',', $types));
                },
                'next_day_threshold' => function($threshold) use ($dashboardSettings) {
                    $dashboardSettings->setNextDayThresholdTime($threshold);
                },
                'skip_weekends' => function($skipWeekends) use ($dashboardSettings) {
                    $dashboardSettings->setSkipWeekends($skipWeekends);
                },
                'past_days' => function($days) use ($dashboardSettings) {
                    $dashboardSettings->setNumberPastDays($days);
                },
                'future_days' => function($days) use ($dashboardSettings) {
                    $dashboardSettings->setNumberFutureDays($days);
                }
            ];

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_dashboard');
        }

        return $this->render('admin/settings/dashboard.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/notifications', name: 'admin_settings_notifications')]
    public function notifications(Request $request, NotificationSettings $notificationSettings, EnumStringConverter $enumStringConverter): Response {
        $builder = $this->createFormBuilder();
        $builder
            ->add('email_enabled', ChoiceType::class, [
                'choices' => ArrayUtils::createArray(array_map(fn(UserType $case) => $case->name, UserType::cases()), UserType::cases()),
                'choice_label' => fn(UserType $userType) => $enumStringConverter->convert($userType),
                'choice_value' => fn(UserType $userType) => $userType->value,
                'expanded' => true,
                'multiple' => true,
                'label' => 'admin.settings.notifications.email.label',
                'help' => 'admin.settings.notifications.email.help',
                'data' => $notificationSettings->getEmailEnabledUserTypes(),
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'email_enabled' => function($types) use ($notificationSettings) {
                    $notificationSettings->setEmailEnabledUserTypes($types);
                }
            ];

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_notifications');
        }

        return $this->render('admin/settings/notifications.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/absences', name: 'admin_settings_absences')]
    public function absences(Request $request, StudentAbsenceSettings $settings): Response {
        $builder = $this->createFormBuilder();
        $builder
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'data' => $settings->isEnabled(),
                'label' => 'admin.settings.student_absences.enabled',
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('recipient', EmailType::class, [
                'required' => false,
                'data' => $settings->getRecipient(),
                'label' => 'admin.settings.student_absences.recipient.label',
                'help' => 'admin.settings.student_absences.recipient.help'
            ])
            ->add('introduction_text', MarkdownType::class, [
                'required' => false,
                'data' => $settings->getIntroductionText(),
                'label' => 'admin.settings.student_absences.introduction_text.label',
                'help' => 'admin.settings.student_absences.introduction_text.help'
            ])
            ->add('privacy_url', TextType::class, [
                'required' => true,
                'data' => $settings->getPrivacyUrl(),
                'label' => 'admin.settings.student_absences.privacy_url.label',
                'help' => 'admin.settings.student_absences.privacy_url.help'
            ])
            ->add('retention_days', IntegerType::class, [
                'required' => true,
                'data' => $settings->getRetentionDays(),
                'label' => 'admin.settings.student_absences.retention_days.label',
                'help' => 'admin.settings.student_absences.retention_days.help',
                'constraints' => [
                    new GreaterThanOrEqual(0)
                ]
            ])
            ->add('next_day_threshold', TimeType::class, [
                'label' => 'admin.settings.dashboard.next_day_threshold.label',
                'help' => 'admin.settings.dashboard.next_day_threshold.help',
                'data' => $settings->getNextDayThresholdTime(),
                'required' => false,
                'input' => 'string',
                'input_format' => 'H:i',
                'widget' => 'single_text'
            ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'enabled' => function($enabled) use ($settings) {
                    $settings->setEnabled($enabled);
                },
                'recipient' => function($recipient) use ($settings) {
                    $settings->setRecipient($recipient);
                },
                'privacy_url' => function($url) use ($settings) {
                    $settings->setPrivacyUrl($url);
                },
                'retention_days' => function($days) use ($settings) {
                    $settings->setRetentionDays($days);
                },
                'introduction_text' => function($text) use ($settings) {
                    $settings->setIntroductionText($text);
                },
                'next_day_threshold' => function($threshold) use ($settings) {
                    $settings->setNextDayThresholdTime($threshold);
                },
            ];

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_absences');
        }

        return $this->render('admin/settings/absences.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/exams', name: 'admin_settings_exams')]
    public function exams(Request $request, ExamSettings $examSettings, EnumStringConverter $enumStringConverter,
                          GradeRepositoryInterface $gradeRepository, Sorter $sorter): Response {
        $builder = $this->createFormBuilder();
        $builder
            ->add('visibility', ChoiceType::class, [
                'choices' => ArrayUtils::createArray(array_map(fn(UserType $case) => $case->name, UserType::cases()), UserType::cases()),
                'choice_label' => fn(UserType $userType) => $enumStringConverter->convert($userType),
                'choice_value' => fn(UserType $userType) => $userType->value,
                'expanded' => true,
                'multiple' => true,
                'label' => 'label.visibility',
                'data' => $examSettings->getVisibility(),
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
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
            ->add('window_supervisions', IntegerType::class, [
                'label' => 'admin.settings.exams.window.supervisions.label',
                'help' => 'admin.settings.exams.window.supervisions.help',
                'constraints' => [
                    new Type(['type' => 'integer']),
                    new GreaterThanOrEqual(['value' => 0])
                ],
                'data' => $examSettings->getTimeWindowForStudentsToSeeSupervisions()
            ])
            ->add('notifications_enabled', CheckboxType::class, [
                'label' => 'admin.settings.exams.notifications.enabled.label',
                'help' => 'admin.settings.exams.notifications.enabled.help',
                'required' => false,
                'data' => $examSettings->isNotificationsEnabled(),
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('notifications_sender', TextType::class, [
                'label' => 'admin.settings.exams.notifications.sender.label',
                'help' => 'admin.settings.exams.notifications.sender.help',
                'required' => false,
                'data' => $examSettings->getNotificationSender()
            ])
            ->add('notifications_replyaddress', EmailType::class, [
                'label' => 'admin.settings.exams.notifications.reply_address.label',
                'help' => 'admin.settings.exams.notifications.reply_address.help',
                'required' => false,
                'data' => $examSettings->getNotificationReplyToAddress()
            ])
            ->add('number_of_exams_day', IntegerType::class, [
                'label' => 'admin.settings.exams.planning.number_of_exams_day.label',
                'help' => 'admin.settings.exams.planning.number_of_exams_day.help',
                'required' => true,
                'data' => $examSettings->getMaximumNumberOfExamsPerDay()
            ])
            ->add('visible_grades', ChoiceType::class, [
                'label' => 'admin.settings.exams.visible_grades.label',
                'help' => 'admin.settings.exams.visible_grades.help',
                'choices' => ArrayUtils::createArrayWithKeysAndValues($gradeRepository->findAll(), fn(Grade $grade) => $grade->getName(), fn(Grade $grade) => $grade->getId()),
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'size' => 10
                ],
                'data' => $examSettings->getVisibleGradeIds()
            ]);

        $grades = $gradeRepository->findAll();
        $sorter->sort($grades, GradeNameStrategy::class);

        foreach($grades as $grade) {
            $builder->add(sprintf('number_of_exams_week_%d', $grade->getId()), IntegerType::class, [
                'label' => 'admin.settings.exams.planning.number_of_exams_week.label',
                'label_translation_parameters' => [
                    '%grade%' => $grade->getName()
                ],
                'help' => 'admin.settings.exams.planning.number_of_exams_week.help',
                'required' => true,
                'data' => $examSettings->getMaximumNumberOfExamsPerWeek($grade)
            ]);
        }

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
                'window_supervisions' => function(int $window) use ($examSettings) {
                    $examSettings->setTimeWindowForStudentsToSeeSupervisions($window);
                },
                'notifications_enabled' => function(bool $enabled) use ($examSettings) {
                    $examSettings->setNotificationsEnabled($enabled);
                },
                'notifications_sender' => function(?string $sender) use ($examSettings) {
                    $examSettings->setNotificationSender($sender);
                },
                'notifications_replyaddress' => function(?string $address) use($examSettings) {
                    $examSettings->setNotificationReplyToAddress($address);
                },
                'number_of_exams_day' => function(int $number) use ($examSettings) {
                    $examSettings->setMaximumNumberOfExamsPerDay($number);
                },
                'visible_grades' => function(?array $visibleGrades) use($examSettings) {
                    $examSettings->setVisibleGradeIds($visibleGrades ?? [ ]);
                }
            ];

            foreach($grades as $grade) {
                $map[sprintf('number_of_exams_week_%d', $grade->getId())] = function(int $number) use($grade, $examSettings) {
                    $examSettings->setMaximumNumberOfExamsPerWeek($grade, $number);
                };
            }

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_exams');
        }

        return $this->render('admin/settings/exams.html.twig', [
            'form' => $form->createView(),
            'grades' => $grades
        ]);
    }

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
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('grades_course_names', ChoiceType::class, [
                'label' => 'admin.settings.timetable.grades_course_names.label',
                'help' => 'admin.settings.timetable.grades_course_names.help',
                'choices' => ArrayUtils::createArrayWithKeysAndValues($gradeRepository->findAll(), fn(Grade $grade) => $grade->getName(), fn(Grade $grade) => $grade->getId()),
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'size' => 10
                ],
                'data' => $timetableSettings->getGradeIdsWithCourseNames()
            ])
            ->add('grades_membership_types', ChoiceType::class, [
                'label' => 'admin.settings.timetable.grades_membership_types.label',
                'help' => 'admin.settings.timetable.grades_membership_types.help',
                'choices' => ArrayUtils::createArrayWithKeysAndValues($gradeRepository->findAll(), fn(Grade $grade) => $grade->getName(), fn(Grade $grade) => $grade->getId()),
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'size' => 10
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

    #[Route(path: '/substitutions', name: 'admin_settings_substitutions')]
    public function substitutions(Request $request, SubstitutionSettings $substitutionSettings, EnumStringConverter $enumStringConverter): Response {
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
                'data' => $substitutionSettings->skipWeekends(),
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('absence_visibility', ChoiceType::class, [
                'choices' => ArrayUtils::createArray(array_map(fn(UserType $case) => $case->name, UserType::cases()), UserType::cases()),
                'choice_label' => fn(UserType $userType) => $enumStringConverter->convert($userType),
                'choice_value' => fn(UserType $userType) => $userType->value,
                'expanded' => true,
                'multiple' => true,
                'label' => 'label.absence_visibility',
                'data' => $substitutionSettings->getAbsenceVisibility(),
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('notifications_enabled', CheckboxType::class, [
                'label' => 'admin.settings.substitutions.notifications.enabled.label',
                'help' => 'admin.settings.substitutions.notifications.enabled.help',
                'required' => false,
                'data' => $substitutionSettings->isNotificationsEnabled(),
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('notifications_sender', TextType::class, [
                'label' => 'admin.settings.substitutions.notifications.sender.label',
                'help' => 'admin.settings.substitutions.notifications.sender.help',
                'required' => false,
                'data' => $substitutionSettings->getNotificationSender()
            ])
            ->add('notifications_replyaddress', EmailType::class, [
                'label' => 'admin.settings.substitutions.notifications.reply_address.label',
                'help' => 'admin.settings.substitutions.notifications.reply_address.help',
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
                'absence_visibility' => function(array $visibility) use ($substitutionSettings) {
                    $substitutionSettings->setAbsenceVisibility($visibility);
                },
                'notifications_enabled' => function(bool $enabled) use ($substitutionSettings) {
                    $substitutionSettings->setNotificationsEnabled($enabled);
                },
                'notifications_sender' => function(?string $sender) use($substitutionSettings) {
                    $substitutionSettings->setNotificationSender($sender);
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
            ->add('fallback_section', ChoiceType::class, [
                'label' => 'label.section',
                'placeholder' => 'label.choose',
                'choices' => ArrayUtils::createArrayWithKeysAndValues(
                    $sectionRepository->findAll(),
                    fn(Section $section) => $section->getDisplayName(),
                    fn(Section $section) => $section->getId()
                ),
                'data' => $settings->getFallbackSection()
            ]);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'rules' => function(array $rules) use($settings) {
                    $settings->setExamRules($rules);
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

    #[Route(path: '/book', name: 'admin_settings_book')]
    public function book(Request $request, BookSettings $settings, GradeRepositoryInterface $gradeRepository): Response {
        $builder = $this->createFormBuilder();
        $builder->add('grades_grade_teacher_excuses', ChoiceType::class, [
                'label' => 'admin.settings.book.excuses.grades_grade_teacher_excuses.label',
                'help' => 'admin.settings.book.excuses.grades_grade_teacher_excuses.help',
                'choices' => ArrayUtils::createArrayWithKeysAndValues($gradeRepository->findAll(), fn(Grade $grade) => $grade->getName(), fn(Grade $grade) => $grade->getId()),
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'size' => 10
                ],
                'data' => $settings->getGradesGradeTeacherExcuses()
            ])
            ->add('grades_tuition_teacher_excuses', ChoiceType::class, [
                'label' => 'admin.settings.book.excuses.grades_tuition_teacher_excuses.label',
                'help' => 'admin.settings.book.excuses.grades_tuition_teacher_excuses.help',
                'choices' => ArrayUtils::createArrayWithKeysAndValues($gradeRepository->findAll(), fn(Grade $grade) => $grade->getName(), fn(Grade $grade) => $grade->getId()),
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'size' => 10
                ],
                'data' => $settings->getGradesTuitionTeacherExcuses()
            ]);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'grades_grade_teacher_excuses' => function(array $ids) use($settings) {
                    $settings->setGradesGradeTeacherExcuses($ids);
                },
                'grades_tuition_teacher_excuses' => function(array $ids) use($settings) {
                    $settings->setGradesTuitionTeacherExcuses($ids);
                }
            ];

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_book');
        }

        return $this->render('admin/settings/book.html.twig', [
            'form' => $form->createView()
        ]);
    }

}