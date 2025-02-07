<?php

namespace App\Controller;

use App\Entity\StudentAbsence;
use App\Entity\StudentAbsenceAttachment;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Http\FlysystemFileResponse;
use App\Repository\StudentAbsenceRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Settings\StudentAbsenceSettings;
use DateTime;
use Exception;
use League\Flysystem\FilesystemOperator;
use Mimey\MimeTypes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[Route('/absence/export')]
#[IsFeatureEnabled(Feature::StudentAbsence)]
class StudentAbsenceExportController extends AbstractController {

    #[Route('', name: 'export_student_absences')]
    public function index(StudentAbsenceRepositoryInterface $repository, Request $request): Response {
        $start = $this->getDateTimeFromRequest($request, 'start');
        $end = $this->getDateTimeFromRequest($request, 'end');
        $uuids = [ ];

        if($start !== null && $end !== null && $start < $end) {
            $uuids = $repository->findAllUuids($start, $end);
        }

        return $this->render('absences/students/export/index.html.twig', [
            'start' => $start,
            'end' => $end,
            'uuids' => $uuids
        ]);
    }

    private function getDateTimeFromRequest(Request $request, string $name): ?DateTime {
        $dateAsString = $request->query->get($name);

        if($dateAsString === null) {
            return null;
        }

        try {
            return new DateTime($dateAsString);
        } catch(Exception $e) {
            return null;
        }
    }

    #[Route('/{uuid}', name: 'export_student_absence')]
    public function absence(StudentAbsence $absence, SectionResolverInterface $sectionResolver, Environment $twig): Response {
        $section = $sectionResolver->getSectionForDate($absence->getFrom()->getDate());

        $json = [
            'uuid' => $absence->getUuid()->toString(),
            'from' => [
                'date' => $absence->getFrom()->getDate()->format('Y-m-d'),
                'lesson' => $absence->getFrom()->getLesson()
            ],
            'until' => [
                'date' => $absence->getUntil()->getDate()->format('Y-m-d'),
                'lesson' => $absence->getUntil()->getLesson()
            ],
            'student' => [
                'uuid' => $absence->getStudent()->getUuid()->toString(),
                'external_id' => $absence->getStudent()->getExternalId(),
                'firstname' => $absence->getStudent()->getFirstname(),
                'lastname' => $absence->getStudent()->getLastname(),
                'grade' => $section !== null ? $absence->getStudent()->getGrade($section)?->getName() : null
            ],
            'metadata' => $twig->render('absences/students/export/metadata.txt.twig', [
                'absence' => $absence
            ]),
            'attachments' => [ ]
        ];

        foreach($absence->getAttachments() as $attachment) {
            $json['attachments'][] = [
                'uuid' => $attachment->getUuid()->toString(),
                'filename' => $attachment->getFilename()
            ];
        }

        return $this->json($json);
    }

    #[Route('/attachment/{uuid}', name: 'export_student_absence_attachment')]
    public function attachment(StudentAbsenceAttachment $attachment, FilesystemOperator $studentAbsenceFilesystem, MimeTypes $mimeTypes, StudentAbsenceSettings $settings): Response {
        if($studentAbsenceFilesystem->fileExists($attachment->getPath()) !== true) {
            throw new NotFoundHttpException();
        }

        $extension = pathinfo($attachment->getFilename(), PATHINFO_EXTENSION);

        return new FlysystemFileResponse(
            $studentAbsenceFilesystem,
            $attachment->getPath(),
            $attachment->getFilename(),
            $mimeTypes->getMimeType($extension)
        );
    }
}