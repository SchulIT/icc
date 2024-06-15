<?php

namespace App\Book\Grade\Export\ZP10;

class View {
    public const columnNachname = 'Name';
    public const columnVorname = 'Vorname';
    public const columnGeburtstag = 'Geb-Datum';
    public const columnFach = 'Fach';
    public const columnAbschluss = 'Abschlussnote';
    public const columnVornote = 'Vornote';
    public const columnSchriftlich = 'Note schr. Prüfung';
    public const columnMuendlich = 'Note mündl. Prüfung';

    /**
     * @param Row[] $rows
     */
    public function __construct(private readonly array $rows) {

    }

    /**
     * @return Row[]
     */
    public function getRows(): array {
        return $this->rows;
    }

    public function getColumnNachname(): string {
        return self::columnNachname;
    }

    public function getColumnVorname(): string {
        return self::columnVorname;
    }

    public function getColumnGeburtstag(): string {
        return self::columnGeburtstag;
    }

    public function getColumnFach(): string {
        return self::columnFach;
    }

    public function getColumnAbschluss(): string {
        return self::columnAbschluss;
    }

    public function getColumnVornote(): string {
        return self::columnVornote;
    }

    public function getColumnSchriftlich(): string {
        return self::columnSchriftlich;
    }

    public function getColumnMuendlich(): string {
        return self::columnMuendlich;
    }
}