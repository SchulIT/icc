# Upgrade von 2.1 auf 2.2

## Veränderungen unter Haube

* keine

## Neue Rollen

Es wurden folgende neuen Rollen hinzugefügt

* ROLE_TEACHER_ABSENCE_VIEWER (darf Absenzen aller Lehrkräfte anzeigen)
* ROLE_RESOURCE_RESERVATION_VIEWER
* ROLE_RESOURCE_RESERVATION_CREATOR
* ROLE_MESSAGE_VIEWER
* ROLE_DOCUMENT_VIEWER
* ROLE_APPOINTMENT_VIEWER
* ROLE_EXAM_VIEWER
* ROLE_LISTS_VIEWER
* ROLE_LISTS_EXPORTER

Folgende Rollen wurden gelöscht:
* ROLE_KIOSK: Diese Rolle kann durch eine beliebige Anzahl an *_VIEWER-Rollen (siehe oben) ersetzt werden

## Neue Features / Verbesserungen

Der zugrundeliegende Milestone 2.2 ist [auf GitHub](https://github.com/SchulIT/icc/milestone/14?closed=1) zu finden.

### Allgemeine Verbesserungen & Bugfixes

### Anwesenheit einsehbar

Man kann Lernenden und Eltern ermöglichen, die Anwesenheit einzusehen. Dazu muss die entsprechende Option in den
Einstellungen vom Unterrichtsbuch aktiviert werden (diese Option ist standardmäßig deaktiviert).

### Aufbewahrungsrichtlinie im Auditlog für importierte Daten

Es ist nun möglich, das Auditlog für importierte Daten nur für die letzten N Tage beizubehalten. So können große
Datenbanken vermieden werden. Die Anzahl der Tage kann über den neuen Konfigurationsparameter `AUDIT_RETENTION_DAYS`
gesteuert werden. Mehr dazu im Handbuch.

Das neue Feature ändert die bisherige Aufbewahrungsrichtlinie (welche nicht vorhanden war) nicht und behält alle
Einträge.

## Upgrade TODO

Das Upgrade beinhaltet eine Migration.

**HINWEIS:** Dieses Update kann im laufenden Betrieb eingespielt werden.

### Nach dem Upgrade TODO

... sind keine zusätzlichen Schritte notwendig. Bei Bedarf können die Import-Einstellungen von Untis angepasst werden.