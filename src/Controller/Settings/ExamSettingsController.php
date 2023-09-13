<?php

namespace App\Controller\Settings;

use App\Converter\EnumStringConverter;
use App\Entity\Grade;
use App\Entity\UserType;
use App\Repository\GradeRepositoryInterface;
use App\Settings\ExamSettings;
use App\Sorting\GradeNameStrategy;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;

#[Route(path: '/settings')]
#[Security("is_granted('ROLE_ADMIN')")]
class ExamSettingsController extends AbstractController {
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
            ->add('reservation_enabled', CheckboxType::class, [
                'label' => 'admin.settings.exams.planning.reservation_enabled.label',
                'help' => 'admin.settings.exams.planning.reservation_enabled.help',
                'required' => false,
                'data' => $examSettings->isRoomReservationAllowed()
            ])
            ->add('visible_grades', ChoiceType::class, [
                'label' => 'admin.settings.exams.visible_grades.label',
                'help' => 'admin.settings.exams.visible_grades.help',
                'choices' => ArrayUtils::createArrayWithKeysAndValues($gradeRepository->findAll(), fn(Grade $grade) => $grade->getName(), fn(Grade $grade) => $grade->getId()),
                'multiple' => true,
                'attr' => [
                    'data-choice' => 'true'
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
                'reservation_enabled' => function(bool $isAllowed) use ($examSettings) {
                    $examSettings->setIsRoomReservationAllowed($isAllowed);
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
}