<?php

namespace App\Controller;

use App\Converter\EnumStringConverter;
use App\Entity\Student;
use App\Form\Import\Untis\RoomImportType;
use App\Untis\Gpu\Room\RoomImporter;
use App\Untis\StudentId\StudentIdGenerator;
use App\Untis\StudentIdFormat;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Import\Untis\CalendarWeekSchoolWeekType;
use App\Form\Import\Untis\ExamImportType;
use App\Form\Import\Untis\SubjectOverrideType;
use App\Form\Import\Untis\SubstitutionGpuImportType;
use App\Form\Import\Untis\SubstitutionHtmlImportType;
use App\Form\Import\Untis\SupervisionImportType;
use App\Form\Import\Untis\TimetableHtmlImportType;
use App\Form\Import\Untis\TimetableImportType;
use App\Form\RegExpType;
use App\Import\ImportException;
use App\Section\SectionResolverInterface;
use App\Settings\UntisSettings;
use App\Untis\Database\Date\DateReader;
use App\Untis\Gpu\Exam\ExamImporter;
use App\Untis\Gpu\Substitution\SubstitutionImporter as GpuSubstitutionImporter;
use App\Untis\Gpu\Supervision\SupervisionImporter;
use App\Untis\Html\HtmlParseException;
use App\Untis\Html\Substitution\SubstitutionImporter as HtmlSubstitutionImporter;
use App\Untis\Html\Timetable\TimetableImporter as HtmlTimetableImporter;
use DateTime;
use Exception;
use League\Csv\Reader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZipArchive;

#[Route(path: '/import')]
#[IsGranted('ROLE_IMPORTER')]
class UntisImportController extends AbstractController {

    #[Route(path: '/settings', name: 'import_untis_settings')]
    public function settings(UntisSettings $settings, Request $request, DateReader $reader, EnumStringConverter $enumStringConverter, StudentIdGenerator $studentIdGenerator): Response {
        $form = $this->createFormBuilder()
            ->add('overrides', CollectionType::class, [
                'entry_type' => SubjectOverrideType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'data' => $settings->getSubjectOverrides()
            ])
            ->add('weeks', CollectionType::class, [
                'entry_type' => CalendarWeekSchoolWeekType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'data' => $settings->getWeekMap()
            ])
            ->add('import_weeks', FileType::class, [
                'label' => 'dates.txt',
                'required' => false
            ])
            ->add('substitution_days', IntegerType::class, [
                'label' => 'import.settings.substitutions.days.label',
                'help' => 'import.settings.substitutions.days.help',
                'data' => $settings->getSubstitutionDays()
            ])
            ->add('collapse_substitutions', CheckboxType::class, [
                'label' => 'import.settings.substitutions.collapse.label',
                'help' => 'import.settings.substitutions.collapse.help',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ],
                'data' => $settings->isSubstitutionCollapsingEnabled()
            ])
            ->add('exam_writers', CheckboxType::class, [
                'label' => 'import.settings.exams.include_students.label',
                'help' => 'import.settings.exams.include_students.help',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ],
                'data' => $settings->alwaysImportExamWriters()
            ])
            ->add('ignore_options_regexp', RegExpType::class, [
                'label' => 'import.settings.exams.ignore_options_regexp.label',
                'help' => 'import.settings.exams.ignore_options_regexp.help',
                'required' => false,
                'data' => $settings->getIgnoreStudentOptionRegExp()
            ])
            ->add('student_id_format', EnumType::class, [
                'label' => 'import.settings.students.format.label',
                'help' => 'import.settings.students.format.help',
                'class' => StudentIdFormat::class,
                'choice_label' => function(StudentIdFormat $format) use($enumStringConverter) {
                    return $enumStringConverter->convert($format);
                },
                'data' => $settings->getStudentIdentifierFormat()
            ])
            ->add('student_id_firstname_letters', IntegerType::class, [
                'label' => 'import.settings.students.firstname_letters.label',
                'help' => 'import.settings.students.firstname_letters.help',
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual(0)
                ],
                'data' => $settings->getStudentIdentifierNumberOfLettersOfFirstname()
            ])
            ->add('student_id_lastname_letters', IntegerType::class, [
                'label' => 'import.settings.students.lastname_letters.label',
                'help' => 'import.settings.students.lastname_letters.help',
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual(0)
                ],
                'data' => $settings->getStudentIdentifierNumberOfLettersOfLastname()
            ])
            ->add('student_id_birthday_format', TextType::class, [
                'label' => 'import.settings.students.birthday_format.label',
                'help' => 'import.settings.students.birthday_format.help',
                'required' => false,
                'constraints' => [
                    new NotBlank(allowNull: true)
                ],
                'data' => $settings->getStudentIdentifierBirthdayFormat()
            ])
            ->add('student_id_separator', TextType::class, [
                'label' => 'import.settings.students.separator.label',
                'help' => 'import.settings.students.separator.help',
                'required' => false,
                'constraints' => [
                    new NotBlank(allowNull: true)
                ],
                'data' => $settings->getStudentIdentifierSeparator()
            ])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $settings->setSubjectOverrides($form->get('overrides')->getData());
            $settings->setWeekMap($form->get('weeks')->getData());
            $settings->setSubstitutionDays($form->get('substitution_days')->getData());
            $settings->setSubstitutionCollapsingEnabled($form->get('collapse_substitutions')->getData());
            $settings->setAlwaysImportExamWriters($form->get('exam_writers')->getData());
            $settings->setIgnoreStudentOptionRegExp($form->get('ignore_options_regexp')->getData());
            $settings->setStudentIdentifierFormat($form->get('student_id_format')->getData());
            $settings->setStudentIdentifierNumberOfLettersOfFirstname($form->get('student_id_firstname_letters')->getData());
            $settings->setStudentIdentifierNumberOfLettersOfLastname($form->get('student_id_lastname_letters')->getData());
            $settings->setStudentIdentifierBirthdayFormat($form->get('student_id_birthday_format')->getData());
            $settings->setStudentIdentifierSeparator($form->get('student_id_separator')->getData());

            /** @var UploadedFile|null $file */
            $file = $form->get('import_weeks')->getData();
            if($file !== null) {
                try {
                    $dates = $reader->readDatabase(Reader::createFromPath($file->getRealPath()));
                    $map = [];

                    foreach ($dates as $date) {
                        $map[] = [
                            'calendar_week' => $date->getCalendarWeek(),
                            'school_week' => $date->getSchoolWeek()
                        ];
                    }
                    $settings->setWeekMap($map);
                } catch (ImportException $exception) {
                    $this->addFlash('error', $exception->getMessage());
                }
            }

            $this->addFlash('success', 'import.settings.success');
            return $this->redirectToRoute('import_untis_settings');
        }

        $student = (new Student())
            ->setFirstname('Erika')
            ->setLastname('Musterfrau')
            ->setBirthday((new DateTime())->setTime(0,0,0));

        $preview = $studentIdGenerator->generate($student);

        return $this->render('import/settings.html.twig', [
            'form' => $form->createView(),
            'student_preview' => $preview
        ]);
    }

    #[Route(path: '/substitutions/gpu', name: 'import_untis_substitutions_gpu')]
    public function substitutionsGpu(Request $request, GpuSubstitutionImporter $importer, TranslatorInterface $translator, UntisSettings $settings): Response {
        $data = [ ];
        if($settings->getSubstitutionDays() > 0) {
            $data = [
                'start' => new DateTime('today'),
                'end' => (new DateTime('today'))->modify('+' . $settings->getSubstitutionDays() . ' days')
            ];
        }

        $form = $this->createForm(SubstitutionGpuImportType::class, $data);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var DateTime $start */
            $start = $form->get('start')->getData();
            /** @var DateTime $end */
            $end = $form->get('end')->getData();
            /** @var UploadedFile $file */
            $file = $form->get('importFile')->getData();
            /** @var bool $suppressNotifications */
            $suppressNotifications = $form->get('suppressNotifications')->getData();

            $csvReader = Reader::createFromPath($file->getRealPath());
            try {
                $result = $importer->import($csvReader, $start, $end, $suppressNotifications);

                $this->addFlash('success', $translator->trans('import.substitutions.result', [
                    '%added%' => count($result->getAdded()),
                    '%ignored%' => count($result->getIgnored()),
                    '%updated%' => count($result->getUpdated()),
                    '%removed%' => count($result->getRemoved())
                ]));

                return $this->redirectToRoute('import_untis_substitutions_gpu');
            } catch (ImportException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('import/substitutions_gpu.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/substitutions/html', name: 'import_untis_substitutions_html')]
    public function substitutionsHtml(Request $request, HtmlSubstitutionImporter $importer, TranslatorInterface $translator, UntisSettings $settings): Response {
        $form = $this->createForm(SubstitutionHtmlImportType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $files */
            $files = $form->get('importFiles')->getData();
            /** @var bool $suppressNotifications */
            $suppressNotifications = $form->get('suppressNotifications')->getData();

            try {
                for($idx = 0; $idx < count($files); $idx++) {
                    $file = $files[$idx];
                    $isLast = $idx === (count($files) - 1);

                    $importer->import($file->getContent(), $isLast === false || $suppressNotifications);
                }

                $this->addFlash('success', 'import.substitutions.html.success');
                return $this->redirectToRoute('import_untis_substitutions_html');
            } catch (Exception $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('import/substitutions_html.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/supervisions', name: 'import_untis_supervisions')]
    public function supervisions(Request $request, SupervisionImporter $importer, TranslatorInterface $translator): Response {
        $form = $this->createForm(SupervisionImportType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var DateTime $start */
            $start = $form->get('start')->getData();
            /** @var DateTime $end */
            $end = $form->get('end')->getData();
            /** @var UploadedFile $file */
            $file = $form->get('importFile')->getData();

            $csvReader = Reader::createFromPath($file->getRealPath());
            try {
                $result = $importer->import($csvReader, $start, $end);

                $this->addFlash('success', $translator->trans('import.supervisions.result', [
                    '%added%' => count($result->getAdded())
                ]));

                return $this->redirectToRoute('import_untis_supervisions');
            } catch (ImportException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('import/supervisions.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/exams', name: 'import_untis_exams')]
    public function exams(Request $request, ExamImporter $importer, TranslatorInterface $translator, SectionResolverInterface $sectionResolver): Response {
        $currentSection = $sectionResolver->getCurrentSection();
        $data = [ ];
        if($currentSection !== null) {
            $data['start'] = $currentSection->getStart();
            $data['end'] = $currentSection->getEnd();
        }

        $form = $this->createForm(ExamImportType::class, $data);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var DateTime $start */
            $start = $form->get('start')->getData();
            /** @var DateTime $end */
            $end = $form->get('end')->getData();
            /** @var UploadedFile $tuitionFile */
            $tuitionFile = $form->get('tuitionFile')->getData();
            /** @var UploadedFile $examFile */
            $examFile = $form->get('examFile')->getData();
            /** @var bool $suppressNotifications */
            $suppressNotifications = $form->get('suppressNotifications')->getData();

            try {
                $result = $importer->import(Reader::createFromPath($examFile->getRealPath()), Reader::createFromPath($tuitionFile->getRealPath()), $start, $end, $suppressNotifications);

                $this->addFlash('success', $translator->trans('import.exams.result', [
                    '%added%' => is_countable($result->getAdded()) ? count($result->getAdded()) : 0,
                    '%ignored%' => is_countable($result->getIgnored()) ? count($result->getIgnored()) : 0,
                    '%updated%' => is_countable($result->getUpdated()) ? count($result->getUpdated()) : 0,
                    '%removed%' => is_countable($result->getRemoved()) ? count($result->getRemoved()) : 0
                ]));

                return $this->redirectToRoute('import_untis_exams');
            } catch (ImportException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('import/exams.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/rooms', name: 'import_untis_rooms')]
    public function rooms(Request $request, RoomImporter $roomImporter, TranslatorInterface $translator): Response {
        $form = $this->createForm(RoomImportType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $roomsFile */
            $roomsFile = $form->get('importFile')->getData();

            try {
                $result = $roomImporter->import(Reader::createFromPath($roomsFile->getRealPath()));

                $this->addFlash('success', $translator->trans('import.rooms.result', [
                    '%added%' => count($result->getAdded()),
                    '%ignored%' => count($result->getIgnored()),
                    '%updated%' => count($result->getUpdated()),
                    '%removed%' => count($result->getRemoved())
                ]));

                return $this->redirectToRoute('import_untis_rooms');
            } catch (ImportException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('import/rooms.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/timetable/html', name: 'import_untis_timetable_html')]
    public function timetableHtml(Request $request, HtmlTimetableImporter $importer, TranslatorInterface $translator): Response {
        $form = $this->createForm(TimetableHtmlImportType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var DateTime $start */
            $start = $form->get('start')->getData();
            /** @var DateTime $end */
            $end = $form->get('end')->getData();
            /** @var UploadedFile|null $grades */
            $grades = $form->get('grades')->getData();
            /** @var UploadedFile|null $subjects */
            $subjects = $form->get('subjects')->getData();

            try {
                $gradesHtml = $grades !== null ? $this->readZip($grades) : [ ];
                $subjectsHtml = $subjects !== null ? $this->readZip($subjects) : [ ];

                $result = $importer->import($gradesHtml, $subjectsHtml, $start, $end);

                $this->addFlash('success', $translator->trans('import.timetable.html.result', [
                    '%added%' => count($result->getAdded())
                ]));

                return $this->redirectToRoute('import_untis_timetable_html');
            } catch (HtmlParseException|ImportException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('import/timetable_html.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function readZip(UploadedFile $file): array {
        $html = [ ];

        $zip = new ZipArchive();
        $zip->open($file->getRealPath(), ZipArchive::RDONLY);
        for($idx = 0; $idx < $zip->numFiles; $idx++) {
            $html[] = $zip->getFromIndex($idx);
        }
        $zip->close();

        return $html;
    }
}