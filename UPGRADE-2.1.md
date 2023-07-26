# Upgrade von 2.0 auf 2.1

## Veränderungen unter Haube

* es wird nun PHP 8.2 vorausgesetzt
* es wird nun auf Yarn verzichtet zugunsten von NPM+Webpack
* es wird nun Bootstrap 5 verwendet

## Neue Features / Verbesserungen

Der zugrundeliegende Milestone 2.1 ist [auf GitHub](https://github.com/SchulIT/icc/milestone/12?closed=1) zu finden.

### Übersichtsseite

* Hausaufgaben werden auf der Übersichtsseite der SuS angezeigt
* Geburtstage von Lehrkräften und Lernenden werden angezeigt (sofern eintragen)

### Public Display

* Anzeige von Geburtstagen (falls gewünscht)
* Anzeige eines Countdowns bis zu einem gewünschten Datum (z.B. letzter Schultag)

### Digitales Unterrichtsbuch

* PDF-Export unterstützt nun Unicode-Zeichen (und somit auch Umlaute vernünftig) - [Handbuch](https://docs.schulit.de/icc/admin/getting_started/settings/book) beachten
* Freitextfeld im Klassen/Kurs-Unterrichtsbuch z.B. zum Eintragen von Klassen- bzw. Kurssprecher
* Bei Entfällen gibt es nun ein Aufgaben-Feld
* Automatischer Entfall bei Ferien/Feiertagen
* Verbesserungen bei den Abwesenheitsvorschlägen
* Nicht-Aktive Lernende im Unterrichtsbuch berücksichtigen (konkret: ausblenden)
* Benachrichtigung bei Klassenbucheintrag

### NEU: Notenmodul

* Notenmodul zum Eintragen von Klassenarbeitsnoten bzw. Klausur/SoMi-Noten
* Noten werden im PDF-Export des Unterrichtsbuches automatisch eingetragen

### NEU: Benachrichtigungen

* Benachrichtigungen via Web-GUI
* Benachrichtigungen via Pushover - siehe [Handbuch](https://docs.schulit.de/icc/admin/getting_started/settings/notifications)
* siehe [Handbuch](https://docs.schulit.de/icc/features/notifications)

### NEU: Lernplattformen

* Es können nun Lernplattformen (aus SchILD NRW) importiert und angezeigt werden

### Abwesenheitsmeldungen Lehrkräfte

* es ist nun möglich, dass sich Lehrkräfte über das ICC krankmelden
* bei der Krankmeldung können Informationen zum Vertretungsunterricht mitgegeben werden

### Abwesenheitsmeldungen Lernende

* Meldungen können nun gelöscht werden
* Massen-Abwesenheitsmeldungen (z.B. für Schulveranstaltungen)
* Abwesenheitsmeldung kann als Entschuldigung übernommen werden (per Mausklick)
* Abwesenheitsmeldungen außerhalb der angelegten Schuljahresabschnitte blockieren

### Klausurplan

* Abwesenheiten von Lernenden werden in der Detail-Ansicht angezeigt

### Stundenplan

* Es kann nun ein Aufsichtsplan angezeigt werden
* Es wird auf Stundenplanperioden verzichtet - Unterrichtsstunden werden nun pro Tag abgespeichert

### Import

* Die Untis-Datei für Räume kann nun über die Web GUI importiert werden

## Upgrade

Das Upgrade beinhaltet mehrere Migrationen. 

**WICHTIG:** Dieses Update sollte nicht im laufenden Betrieb eingespielt werden, sondern zwischen den Schuljahren. Es
enthält eine Migration, die den gesamten Stundenplan löscht, da dieser nun ohne Perioden arbeitet.

### Wichtige Hinweise zum Aktualisierungsprozess

* Es können Meldungen zu Deprecations auftreten - diese können ignoriert werden
* Bei Fehlermeldungen beim Ausführungen von `php bin/console ...` Befehlen, kann es notwendig sein, zuvor `rm -rf var/cache/prod` auszuführen

### Nach dem Upgrade

* In den Einstellungen gibt es nun zwei wichtige Einstellungen zum Unterrichtsbuch. Diese müssen nach dem Upgrade gesetzt werden.
* Das Nodenmodul sollte konfiguriert werden, damit es nutzbar ist - siehe [Handbuch](https://docs.schulit.de/icc/admin/getting_started/settings/gradebook)