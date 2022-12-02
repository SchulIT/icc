<?php

namespace App\Untis\Html\Substitution;

use App\Settings\UntisHtmlSettings;
use App\Untis\Html\AbstractHtmlReader;
use DateTime;
use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Exception;

class SubstitutionReader extends AbstractHtmlReader {

    private const DateSelectorClass = 'mon_title';
    private const InfoTableSelectorClass = 'info';
    private const InfoEntrySelectorClass = 'info';
    private const SubstitutionsTableSelectorClass = 'mon_list';
    private const SubstitutionEntryClass = 'list';

    private const IgnoredSubstitutionTypes = [ 'Veranst.', 'Klausur' ];

    /**
     * @param InfotextReaderInterface[] $infotextReader
     */
    public function __construct(private iterable $infotextReader, private TableCellParser $tableCellParser, private UntisHtmlSettings $settings)
    {
    }

    public function readHtml(string $html): SubstitutionResult {
        libxml_use_internal_errors(true); libxml_clear_errors();

        $html = $this->fixHtml($html);

        $document = new DOMDocument();
        $document->loadHTML($html);
        $xpath = new DOMXPath($document);
        $date = $this->parseDate($xpath);
        $result = new SubstitutionResult($date);

        $this->parseInfotexts($result, $xpath);
        $this->parseSubstitutions($result, $xpath);

        return $result;
    }

    private function parseInfotexts(SubstitutionResult $result, DOMXPath $xpath): void {
        $infotexts = [ ];

        $nodes = $xpath->query("//table[@class='" . self::InfoTableSelectorClass . "']//tr[@class='" . self:: InfoEntrySelectorClass . "']");

        for($idx = 1; $idx < (is_countable($nodes) ? count($nodes) : 0); $idx++) { // First item is table header
            $node = $nodes[$idx];
            $childrenCount = is_countable($node->childNodes) ? count($node->childNodes) : 0;

            $identifier = null;
            $content = trim(strip_tags($nodes[$idx]->nodeValue));

            if($childrenCount == 2) {
                $identifier = trim($node->childNodes[0]->nodeValue);
                $content = trim($node->childNodes[1]->nodeValue);
            }

            foreach($this->infotextReader as $reader) {
                if($reader->canHandle($identifier)) {
                    $reader->handle($result, $content);
                }
            }
        }

    }

    private function parseSubstitutions(SubstitutionResult $result, DOMXPath $xpath): void {
        $table = $xpath->query("//table[@class='" . self::SubstitutionsTableSelectorClass . "']");

        if($table === false || count($table) === 0) {
            return;
        }

        $nodes = $xpath->query("//table[@class='" . self::SubstitutionsTableSelectorClass . "']//tr[starts-with(@class, '" . self::SubstitutionEntryClass . "')]", $table[0]);

        if($nodes === false || $nodes->count() < 2) {
            // empty list or column headers only
            return;
        }

        $order = $this->getColumnOrder($nodes, $xpath);

        for($idx = 1; $idx < $nodes->count(); $idx++) {
            /** @var DOMNode $node */
            $node = $nodes[$idx];

            $substitution = new Substitution();
            $substitution->setId($this->tableCellParser->parseIntegerColumn($node->childNodes[$order->getIdColumn()]->nodeValue));
            $substitution->setDate($result->getDateTime());

            $parsedLesson = $this->tableCellParser->parseLessonColumn($node->childNodes[$order->getLessonColumn()]->nodeValue);
            $substitution->setLessonStart($parsedLesson->getLessonStart());
            $substitution->setLessonEnd($parsedLesson->getLessonEnd());
            $substitution->setIsSupervision($parsedLesson->isBefore());

            if($parsedLesson->isBefore()) {
                $substitution->setLessonStart($parsedLesson->getLessonEnd());
            }

            $substitution->setType($this->tableCellParser->parseStringOrNullColumn($node->childNodes[$order->getTypeColumn()]->nodeValue));
            $substitution->setSubject($this->tableCellParser->parseStringOrNullColumn($node->childNodes[$order->getSubjectColumn()]->nodeValue));
            $substitution->setReplacementSubject($this->tableCellParser->parseStringOrNullColumn($node->childNodes[$order->getReplacementSubjectColumn()]->nodeValue));
            $substitution->setRooms($this->tableCellParser->parseMultiStringColumn($node->childNodes[$order->getRoomColumn()]->nodeValue));
            $substitution->setReplacementRooms($this->tableCellParser->parseMultiStringColumn($node->childNodes[$order->getReplacementRoomColumn()]->nodeValue));
            $substitution->setRemark($this->tableCellParser->parseStringOrNullColumn($node->childNodes[$order->getRemarkColumn()]->nodeValue));
            $substitution->setTeachers($this->tableCellParser->parseMultiStringColumn($node->childNodes[$order->getTeachersColumn()]->nodeValue));
            $substitution->setReplacementTeachers($this->tableCellParser->parseMultiStringColumn($node->childNodes[$order->getReplacementTeachersColumn()]->nodeValue));
            $substitution->setGrades($this->tableCellParser->parseMultiStringColumn($node->childNodes[$order->getGradesColumn()]->nodeValue));
            $substitution->setReplacementGrades($this->tableCellParser->parseMultiStringColumn($node->childNodes[$order->getReplacementGradesColumn()]->nodeValue));

            $isCancelledValue = $this->tableCellParser->parseStringOrNullColumn($node->childNodes[$order->getIsCancelledColumn()]->nodeValue);
            $isCancelled = $isCancelledValue !== null && trim($isCancelledValue) === 'x';

            if($isCancelled === true) {
                $substitution->setReplacementGrades([]);
                $substitution->setReplacementSubject(null);
                $substitution->setReplacementTeachers([]);
                $substitution->setReplacementRooms([]);
            }

            if(!in_array($substitution->getType(), self::IgnoredSubstitutionTypes)) {
                $result->addSubstitution($substitution);
            }
        }
    }

    private function getColumnOrder(DOMNodeList $nodes, DOMXPath $xpath): SubstitutionTableColumnOrder {
        /** @var DOMNode $tr */
        $tr = $nodes[0];

        $headerCells = $xpath->query('./th', $tr);

        $cellIdxes = [ ];
        foreach($headerCells as $idx => $headerCell) {
            $cellIdxes[trim($headerCell->nodeValue)] = $idx;
        }

        return (new SubstitutionTableColumnOrder())
            ->setIdColumn($this->tableCellParser->getCellIndexOrNull($cellIdxes, $this->settings->getIdColumnName()))
            ->setDateColumn($this->tableCellParser->getCellIndexOrNull($cellIdxes, $this->settings->getDateColumnName()))
            ->setLessonColumn($this->tableCellParser->getCellIndexOrNull($cellIdxes, $this->settings->getLessonColumnName()))
            ->setGradesColumn($this->tableCellParser->getCellIndexOrNull($cellIdxes, $this->settings->getGradesColumnName()))
            ->setReplacementGradesColumn($this->tableCellParser->getCellIndexOrNull($cellIdxes, $this->settings->getReplacementGradesColumnName()))
            ->setTeachersColumn($this->tableCellParser->getCellIndexOrNull($cellIdxes, $this->settings->getTeachersColumnName()))
            ->setReplacementTeachersColumn($this->tableCellParser->getCellIndexOrNull($cellIdxes, $this->settings->getReplacementTeachersColumnName()))
            ->setSubjectColumn($this->tableCellParser->getCellIndexOrNull($cellIdxes, $this->settings->getSubjectColumnName()))
            ->setReplacementSubjectColumn($this->tableCellParser->getCellIndexOrNull($cellIdxes, $this->settings->getReplacementSubjectColumnName()))
            ->setRoomColumn($this->tableCellParser->getCellIndexOrNull($cellIdxes, $this->settings->getRoomsColumnName()))
            ->setReplacementRoomColumn($this->tableCellParser->getCellIndexOrNull($cellIdxes, $this->settings->getReplacementRoomsColumnName()))
            ->setTypeColumn($this->tableCellParser->getCellIndexOrNull($cellIdxes, $this->settings->getTypeColumnName()))
            ->setRemarkColumn($this->tableCellParser->getCellIndexOrNull($cellIdxes, $this->settings->getRemarkColumnName()))
            ->setIsCancelledColumn($this->tableCellParser->getCellIndexOrNull($cellIdxes, $this->settings->getIsCancelledColumnName()));
    }

    /**
     * @throws Exception
     */
    private function parseDate(DOMXPath $xpath): DateTime {
        $elements = $xpath->query("//div[@class='" . self::DateSelectorClass . "']");
        $firstElement = $elements !== false ? $elements->item(0) : null;

        if($firstElement === null) {
            throw new Exception('Date was not found.');
        }

        $dateValue = $firstElement->nodeValue;
        $parts = explode(' ', $dateValue);

        $date = DateTime::createFromFormat('j.n.Y', $parts[0]);

        if($date === false) {
            throw new Exception('Date was not parsed correctly.');
        }

        $date->setTime(0, 0, 0);

        return $date;
    }
}