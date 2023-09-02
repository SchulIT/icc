<?php

namespace App\Controller\Settings;

use App\Entity\Grade;
use App\Form\TextCollectionEntryType;
use App\Repository\GradeRepositoryInterface;
use App\Settings\BookSettings;
use App\Utils\ArrayUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Route(path: '/settings')]
#[Security("is_granted('ROLE_ADMIN')")]
class BookSettingsController extends AbstractController {
    #[Route(path: '/book', name: 'admin_settings_book')]
    public function book(Request $request, BookSettings $settings, GradeRepositoryInterface $gradeRepository): Response {
        $builder = $this->createFormBuilder();
        $builder->add('grades_grade_teacher_excuses', ChoiceType::class, [
            'label' => 'admin.settings.book.excuses.grades_grade_teacher_excuses.label',
            'help' => 'admin.settings.book.excuses.grades_grade_teacher_excuses.help',
            'choices' => ArrayUtils::createArrayWithKeysAndValues($gradeRepository->findAll(), fn(Grade $grade) => $grade->getName(), fn(Grade $grade) => $grade->getId()),
            'multiple' => true,
            'attr' => [
                'data-choice' => 'true'
            ],
            'data' => $settings->getGradesGradeTeacherExcuses()
        ])
            ->add('grades_tuition_teacher_excuses', ChoiceType::class, [
                'label' => 'admin.settings.book.excuses.grades_tuition_teacher_excuses.label',
                'help' => 'admin.settings.book.excuses.grades_tuition_teacher_excuses.help',
                'choices' => ArrayUtils::createArrayWithKeysAndValues($gradeRepository->findAll(), fn(Grade $grade) => $grade->getName(), fn(Grade $grade) => $grade->getId()),
                'multiple' => true,
                'attr' => [
                    'data-choice' => 'true'
                ],
                'data' => $settings->getGradesTuitionTeacherExcuses()
            ])
            ->add('exclude_student_status', CollectionType::class, [
                'label' => 'admin.settings.book.exclude_student_status.label',
                'help' => 'admin.settings.book.exclude_student_status.help',
                'required' => false,
                'data' => $settings->getExcludeStudentsStatus(),
                'entry_type' => TextCollectionEntryType::class,
                'entry_options' => [
                    'constraints' => new NotBlank()
                ],
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('regular_font', FileType::class, [
                'required' => false,
                'label' => 'admin.settings.book.font.regular.label',
                'help' => 'admin.settings.book.font.regular.help'
            ])
            ->add('bold_font', FileType::class, [
                'required' => false,
                'label' => 'admin.settings.book.font.bold.label',
                'help' => 'admin.settings.book.font.bold.help'
            ])
            ->add('attendances_visible_for_students_and_parents', CheckboxType::class, [
                'required' => false,
                'label' => 'admin.settings.book.attendances_visible_for_students_and_parents.label',
                'help' => 'admin.settings.book.attendances_visible_for_students_and_parents.help',
                'data' => $settings->isAttendanceVisibleForStudentsAndParentsEnabled()
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
                },
                'exclude_student_status' => function(array $status) use($settings) {
                    $settings->setExcludeStudentsStatus($status);
                },
                'attendances_visible_for_students_and_parents' => function(bool $isEnabled) use($settings) {
                    $settings->setAttendanceVisibleForStudentsAndParentsEnabled($isEnabled);
                }
            ];

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $map = [
                'regular_font' => function(?string $font) use ($settings) {
                    $settings->setRegularFont($font);
                },
                'bold_font' => function(?string $font) use ($settings) {
                    $settings->setBoldFont($font);
                }
            ];

            foreacH($map as $formKey => $callable) {
                /** @var UploadedFile|null $file */
                $file = $form->get($formKey)->getData();

                if($file === null) {
                    continue;
                }

                $callable(base64_encode($file->getContent()));
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_book');
        }

        return $this->render('admin/settings/book.html.twig', [
            'form' => $form->createView()
        ]);
    }
}