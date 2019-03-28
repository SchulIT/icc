<?php

namespace App\Csv;

use League\Csv\CharsetConverter;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class CsvHelper {

    /**
     * Generates CSV content based on the given data (multi-dimensional array). See PHP's fputcsv().
     *
     * @param string[][] $fields
     * @param string $separator The separator which is used to separate the values (default: ,)
     * @return string
     * @throws \Exception
     */
    public function getCsvContent(array $fields, $separator = ','): string {
        $encoder = (new CharsetConverter())
            ->inputEncoding('UTF-8')
            ->outputEncoding('UTF-16LE');

        $writer = Writer::createFromPath('php://temp', 'w');
        $writer->addFormatter($encoder);
        $writer->setOutputBOM(Writer::BOM_UTF16_LE);
        $writer->setDelimiter($separator);
        $writer->insertAll($fields);

        return $writer->getContent();
    }

    /**
     * Gets a HttpResponse with CSV content which can be send to the client.
     *
     * @param string $filename The filename which is used for downloading the file.
     * @param string[][] $fields CSV data (see fputcsv())
     * @param string $separator The separator which is used to separate the values (default: ,)
     * @return Response
     * @throws \Exception
     */
    public function getCsvResponse(string $filename, array $fields, $separator = ','): Response {
        $csv = $this->getCsvContent($fields, $separator);

        $response = new Response($csv);
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-16');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename));

        return $response;
    }
}