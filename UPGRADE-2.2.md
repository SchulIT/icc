# Upgrade von 2.1 auf 2.2

## Veränderungen unter Haube

* keine

## Neue Features / Verbesserungen

Der zugrundeliegende Milestone 2.2 ist [auf GitHub](https://github.com/SchulIT/icc/milestone/14?closed=1) zu finden.

### Allgemeine Verbesserungen & Bugfixes

* Lehrkräfte mit Klassenleitung haben nun bei Klassenfilterungen die Möglichkeit, die eigene Klasse mit einem Mausklick auszuwählen
* Übersichtsdesign bei Dokumenten wiederhergestellt (durch BS5-Upgrade beschädigt)
* Raumübersicht zeigt nun wieder Raumbelegungen an, wenn man mit der Maus über die Belegung fährt (durch BS5-Upgrade beschädigt)

### Import aus Untis

* Man kann nun Vertretungsarten beim HTML-Import nicht berücksichtigten lassen (standardmäßig wird z.B. die Vertretungsart "Klausur" ignoriert)
* Veranstaltungen (Vertretungsart "Veranst." - kann konfiguriert werden) werden nun als solche erkannt (für die nächste Funktion benötigt)
* Absenzen können bereinigt werden (standardmäßig aktiv, kann deaktiviert werden)
  * Absenzen werden nun zusammengefasst für eine bessere Übersicht (bisher wurden sie stupide aus Untis übernommen)
  * Veranstaltungen löschen eine Absenzen in den Unterrichtsstunden der Veranstaltung

### Mitteilungen

* Mitteilungen für Lernende werden jetzt auch für Eltern angezeigt (gilt sowohl für die Übersichtsseite als auch für den Punkt Mitteilungen)

### Übersichtsseite

* Gesperrte Räume werden nun angezeigt

### Raumreservierung

* Gesperrte Räume werden nun berücksichtigt

### Public Display

* Gesperrte Räume werden nun angezeigt

### Digitales Unterrichtsbuch

* es ist nun möglich, Lernende aus der Anwesenheitsliste zu löschen
* es gibt nun Vorschläge zum Löschen von Lernenden aus der Anwesenheitsliste, wenn sie parallele Kurse im Stundenplan haben
* auf der Klassenübersicht werden nur Absenzen von Lernenden der ausgewählten Klasse berücksichtigt

### Abwesenheitsmeldungen Lernende

* Der Entschuldigungsstatus bei Abwesenheitsmeldungen wird nun angezeigt.
* Man kann nun pro Abwesenheitsart zusätzliche Empfänger angeben, an die beim Erstellen eine E-Mail gesendet werden soll (z.B. bei meldepflichtigen Krankheiten an die Schulleitung)

### Stundenplan

* Es kann nun ein Aufsichtsplan angezeigt werden
* Es wird auf Stundenplanperioden verzichtet - Unterrichtsstunden werden nun pro Tag abgespeichert

### Import

* Die Untis-Datei für Räume kann nun über die Web GUI importiert werden

## Upgrade

Das Upgrade beinhaltet eine Migration.

**HINWEIS:** Dieses Update kann im laufenden Betrieb eingespielt werden.

### Nach dem Upgrade

... sind keine zusätzlichen Schritte notwendig. Bei Bedarf können die Import-Einstellungen von Untis angepasst werden.