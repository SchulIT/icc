<?php

namespace App\Settings;

class UntisHtmlSettings extends AbstractSettings {
    public function getIdColumnName(): string {
        return $this->getValue('untis.import.html.columns.id', 'Vtr-Nr.');
    }

    public function setIdColumnName(string $columnName): void {
        $this->setValue('untis.import.html.columns.id', $columnName);
    }

    public function getDateColumnName(): string {
        return $this->getValue('untis.import.html.columns.date', 'Datum');
    }

    public function setDateColumnName(string $columnName): void {
        $this->setValue('untis.import.html.columns.date', $columnName);
    }

    public function getLessonColumnName(): string {
        return $this->getValue('untis.import.html.columns.lesson', 'Stunde');
    }

    public function setLessonColumnName(string $columnName): void {
        $this->setValue('untis.import.html.columns.lesson', $columnName);
    }

    public function getGradesColumnName(): string {
        return $this->getValue('untis.import.html.columns.grades', '(Klasse(n))');
    }

    public function setGradesColumnName(string $columnName): void {
        $this->setValue('untis.import.html.columns.grades', $columnName);
    }

    public function getReplacementGradesColumnName(): string {
        return $this->getValue('untis.import.html.columns.replacement_grades', 'Klasse(n)');
    }

    public function setReplacementGradesColumnName(string $columnName): void {
        $this->setValue('untis.import.html.columns.replacement_grades', $columnName);
    }

    public function getSubjectColumnName(): string {
        return $this->getValue('untis.import.html.columns.subject', 'Fach');
    }

    public function setSubjectColumnName(string $columnName): void {
        $this->setValue('untis.import.html.columns.subject', $columnName);
    }

    public function getReplacementSubjectColumnName(): string {
        return $this->getValue('untis.import.html.columns.replacement_subject', '(Fach)');
    }

    public function setReplacementSubjectColumnName(string $columnName): void {
        $this->setValue('untis.import.html.columns.replacement_subject', $columnName);
    }

    public function getTeachersColumnName(): string {
        return $this->getValue('untis.import.html.columns.teachers', '(Lehrer)');
    }

    public function setTeachersColumnName(string $columnName): void {
        $this->setValue('untis.import.html.columns.teachers', $columnName);
    }

    public function getReplacementTeachersColumnName(): string {
        return $this->getValue('untis.import.html.columns.replacement_teachers', 'Vertreter');
    }

    public function setReplacementTeachersColumnName(string $columnName): void {
        $this->setValue('untis.import.html.columns.replacement_teachers', $columnName);
    }
    public function getRoomsColumnName(): string {
        return $this->getValue('untis.import.html.columns.rooms', '(Raum)');
    }

    public function setRoomsColumnName(string $columnName): void {
        $this->setValue('untis.import.html.columns.rooms', $columnName);
    }

    public function getReplacementRoomsColumnName(): string {
        return $this->getValue('untis.import.html.columns.replacement_rooms', 'Raum');
    }

    public function setReplacementRoomsColumnName(string $columnName): void {
        $this->setValue('untis.import.html.columns.replacement_rooms', $columnName);
    }

    public function getTypeColumnName(): string {
        return $this->getValue('untis.import.html.columns.type', 'Art');
    }

    public function setTypeColumnName(string $columnName): void {
        $this->setValue('untis.import.html.columns.type', $columnName);
    }

    public function getRemarkColumnName(): string {
        return $this->getValue('untis.import.html.columns.remark', 'Text');
    }

    public function setRemarkColumnName(string $columnName): void {
        $this->setValue('untis.import.html.columns.remark', $columnName);
    }
}