<?php

namespace App\Controller;

use App\Entity\TimetablePeriod;
use App\Form\Import\Untis\CalendarWeekSchoolWeekType;
use App\Form\Import\Untis\ExamImportType;
use App\Form\Import\Untis\SubjectOverrideType;
use App\Form\Import\Untis\SubstitutionImportType;
use App\Form\Import\Untis\SupervisionImportType;
use App\Form\RegExpType;
use App\Import\ImportException;
use App\Section\SectionResolverInterface;
use App\Settings\UntisSettings;
use App\Untis\DatabaseDateReader;
use App\Untis\GpuExamImporter;
use App\Untis\GpuSubstitutionImporter;
use App\Untis\GpuSupervisionImporter;
use DateTime;
use League\Csv\Reader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/import")
 * @IsGranted("ROLE_IMPORTER")
 */
class UntisImportController extends AbstractController {

    /**
     * @Route("/settings", name="import_untis_settings")
     */
    public function settings(UntisSettings $settings, Request $request, DatabaseDateReader $reader) {
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
                'required' => false
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

        return $this->render('import/settings.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/substitutions", name="import_untis_substitutions")
     */
    public function substitutions(Request $request, GpuSubstitutionImporter $importer, TranslatorInterface $translator, UntisSettings $settings) {
        $data = [ ];
        if($settings->getSubstitutionDays() > 0) {
            $data = [
                'start' => new DateTime('today'),
                'end' => (new DateTime('today'))->modify('+' . $settings->getSubstitutionDays() . ' days')
            ];
        }

        $form = $this->createForm(SubstitutionImportType::class, $data);
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

                return $this->redirectToRoute('import_untis_substitutions');
            } catch (ImportException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('import/substitutions.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/supervisions", name="import_untis_supervisions")
     */
    public function supervisions(Request $request, GpuSupervisionImporter $importer, TranslatorInterface $translator) {
        $form = $this->createForm(SupervisionImportType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var TimetablePeriod $period */
            $period = $form->get('period')->getData();
            /** @var UploadedFile $file */
            $file = $form->get('importFile')->getData();

            $csvReader = Reader::createFromPath($file->getRealPath());
            try {
                $result = $importer->import($csvReader, $period);

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

    /**
     * @Route("/exams", name="import_untis_exams")
     */
    public function exams(Request $request, GpuExamImporter $importer, TranslatorInterface $translator, SectionResolverInterface $sectionResolver) {
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
                    '%added%' => count($result->getAdded()),
                    '%ignored%' => count($result->getIgnored()),
                    '%updated%' => count($result->getUpdated()),
                    '%removed%' => count($result->getRemoved())
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
}