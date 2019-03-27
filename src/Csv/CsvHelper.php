<?php

namespace App\Csv;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class CsvHelper {

    /**
     * Generates CSV content based on the given data (multi-dimensional array). See PHP's fputcsv().
     *
     * @param string[][] $fields
     * @param string $separator The separator which is used to separate the values (default: ,)
     * @return string
     * @see fputcsv()
     */
    public function getCsvContent(array $fields, $separator = ','): string {
        ob_start();

        $handler = fopen('php://output', 'w');

        if($handler === false) {
            throw new \RuntimeException('Cannot open php://output');
        }

        echo 'sep=' . $separator . PHP_EOL;

        foreach($fields as $row) {
            fputcsv($handler, $row, $separator);
        }

        return (string)ob_get_clean();
    }

    /**
     * Gets a HttpResponse with CSV content which can be send to the client.
     *
     * @param string $filename The filename which is used for downloading the file.
     * @param string[][] $fields CSV data (see fputcsv())
     * @param string $separator The separator which is used to separate the values (default: ,)
     * @return Response
     * @see CsvHelper::getCsvContent()
     */
    public function getCsvResponse(string $filename, array $fields, $separator = ','): Response {
        $csv = $this->getCsvContent($fields, $separator);

        $csv = mb_convert_encoding($csv, 'UTF-16LE', 'UTF-8');
        $csv = chr(255) . chr(254) . $csv; // add UTF-16LE BOM

        $response = new Response($csv);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

        return $response;
    }
}