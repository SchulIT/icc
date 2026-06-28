<?php

namespace App\Book\ReportRemark\Export\Schild;

use App\Book\Entity\ReportRemark;
use App\Book\Repository\ReportRemarkRepositoryInterface;
use App\Common\Entity\Section;
use App\Framework\Utils\ArrayUtils;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

readonly class Exporter {
    public function __construct(
        private ReportRemarkRepositoryInterface $reportRemarkRepository
    ) {

    }

    public function export(ExportRequest $request): string {
        $output = 'Nachname|Vorname|Geburtsdatum|Jahr|Abschnitt' . PHP_EOL;

        $groups = ArrayUtils::createArrayWithKeys(
            $this->reportRemarkRepository->findAll($request->section),
            fn(ReportRemark $reportRemark) => $reportRemark->getStudent()->getId(),
            true
        );

        foreach($groups as $group) {
            $remark = array_first($group);

            $output .= sprintf(
                '%s|%s|%s|%d|%d',
                $remark->getStudent()->getLastname(),
                $remark->getStudent()->getFirstname(),
                $remark->getStudent()->getBirthday()?->format('d.m.Y'),
                $remark->getSection()->getYear(),
                $remark->getSection()->getNumber()
            ) . PHP_EOL;

            $output .= '<V>' . PHP_EOL;

            foreach($group as $remark) {
                $output .= $remark->getRemark() . PHP_EOL;
            }
            $output .= '</V>' . PHP_EOL;
        }

        return $output;
    }

    public function exportResponse(ExportRequest $request): Response {
        $csv = $this->export($request);

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $request->filename
        );

        $response = new Response($csv);
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
