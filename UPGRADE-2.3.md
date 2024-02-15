# Upgrade von 2.2 auf 2.3

## Veränderungen unter Haube

* Das Projekt basiert nun auf Symfony 6.4

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
* ROLE_TOOLS

Folgende Rollen wurden gelöscht:
* ROLE_KIOSK: Diese Rolle kann durch eine beliebige Anzahl an *_VIEWER-Rollen (siehe oben) ersetzt werden

## Neue Features / Verbesserungen

Der zugrundeliegende Milestone 2.3 ist [auf GitHub](https://github.com/SchulIT/icc/milestone/14?closed=1) zu finden.

### Elternsprechtagsplanung

Es können nun Elternsprechtage über das ICC gebucht werden. Mehr dazu [im Handbuch](https://docs.schulit.de/icc/features/parents_day)

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

### Integritätscheck

Mithilfe des Integritätchecks können fehlerhafte Eintragungen im Hinblick auf die Anwesenheit von Lernenden gefunden werden.
Der entsprechende Menüpunkt ist unter *Unterrichtsbuch* zu finden. 

Die Einrichtung ist im [Handbuch](https://docs.schulit.de/icc/admin/guides/integrity_check)

### Verhalten von Abwesenheitsmeldungen überarbeitet

Für Abwesenheitsarten kann nun der Text geändert werden, der ins Unterrichtsbuch übernommen wird. Außerdem kann pro
Art festgelegt werden, wie der Anwesenheitsstatus (anwesend vs. abwesend) und der Entschuldigungsstatus (entschuldigt, offen)
bei der Übernahme ins Unterrichtbuch gesetzt werden soll. Das erlaubt z.B. das Melden der Sportunfähigkeit (wo technisch
gesehen keine Abwesenheit vorliegt).

Es ist außerdem nun möglich, dass Abwesenheitsmeldungen nur für bestimmte Fächer berücksichtigt werden. Beispielsweise
ist eine *Sportunfähigkeit* nur für das Fach *Sport* zu berücksichtigen. Es ist möglich, mehrere Fächer auszuwählen. 

### Priorität An- bzw. Abwesenheitsvorschläge im Unterrichtsbuch

Es ist nun möglich, in den Einstellungen des Unterrichtsbuchs Prioritäten für die Anwesenheitsvorschläge festzulegen. Das 
ermöglicht es, für die eigene Schule und die eingepflegten Abwesenheitsarten zugeschnittene Vorschläge machen zu können.
Das System sammelt zunächst alle möglichen An- oder Abwesenheitsgründe und schlägt nur den vor, der die höchste Priorität
hat.

### Klausurkurse erkennbar

Das System versucht nun, bei Klausurschreibenden die entsprechenden Kurse zuzuordnen. Siehe [Handbuch](https://docs.schulit.de/icc/admin/import/untis/#zuordnung-der-klausurschreibenden-zu-unterrichten)

### Sauberes E-Mail-Limiting

Das Limit von E-Mails wird nun streng eingehalten. Dazu müssen die Konfigurationsvariablen `MAILER_LIMIT` und `MAILER_INTERVAL`
entsprechend gesetzt werden. Siehe [Handbuch](https://docs.schulit.de/icc/admin/install/configuration#mailer_limit)

### Hintergrundaufgaben

Hintergrundaufgaben erledigen zeitintensive Aufgaben wie beispielsweise den Versand von E-Mails. Bisher wurden diese
Hintergrundaufgaben ausschließlich als Cronjob ausgeführt. Das kann jedoch je nach Konfiguration dazu führen, dass die
Aufgaben nicht ordnungsgemäß ausgeführt werden.

Es ist nun möglich, Hintergrundaufgaben nicht als Cronjob sondern mithilfe eines Dienstes (bspw. systemd) auszuführen.
Mehr dazu gibt es im [Handbuch](https://docs.schulit.de/icc/admin/maintenance/messenger).

## Upgrade TODO

Das Upgrade beinhaltet mehrere Migrationen.

**HINWEIS:** Dieses Update kann im laufenden Betrieb eingespielt werden. Es bedarf jedoch ggf. einer Anpassung der 
Konfigurationsdatei.

### Nach dem Upgrade TODO

* Nach Belieben neue Abwesenheitsarten erstellen.
* Prioritäten für die An- und Abwesenheitsvorschläge unter *Einstellungen ➜ Unterrichtsbuch* festlegen. Empfehlen finden sich im Handbuch.