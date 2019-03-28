<?php

namespace App\Tests\Csv;

use App\Csv\CsvHelper;
use PHPUnit\Framework\TestCase;

class CsvHelperTest extends TestCase {

    /**
     * @return string[][]
     */
    private function getData(): array {
        return [
            [ 'Header 1', 'Header 2', 'Header 3'],
            [ 'Row 1.1', 'Row 1.2', 'Row 1.3'],
            [ 'Row 2.1', 'Row 2.2', 'Row 2.3']
        ];
    }

    public function testCsvResponse() {
        $helper = new CsvHelper();
        $response = $helper->getCsvResponse('filename.csv', $this->getData());

        $this->assertEquals('text/csv; charset=UTF-16', $response->headers->get('Content-Type'));
        $this->assertEquals('attachment; filename=filename.csv', $response->headers->get('Content-Disposition'));
    }
}