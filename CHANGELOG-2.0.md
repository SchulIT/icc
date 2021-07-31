# Neu in Version 2.0

**WICHTIG:** Das Update darf nicht im laufenden Schuljahr eingespielt werden,
da es umfangreiche Änderungen an der Datenbank vornimmt. Zudem ist ein initialer
Import notwendig, da bei der Migration der Datenbank Daten verloren gehen.

## Softwarevoraussetzungen

Es wird nun PHP 7.4 vorausgesetzt. Die Nutzung von PHP 8.0 sollte möglich sein, wird aber noch nicht empfohlen. 
Das ICC basiert nun auf Symfony 5.3.

## Schuljahresabschnitte

Das ICC kann nun über einen Schuljahresabschnitt hinaus arbeiten. Somit
muss das ICC am Ende eines Halbjahres nicht zurückgesetzt werden und
kann stattdessen fortgeführt werden.

Auch wenn das ICC grundsätzlich über mehrere Schuljahre hinweg betrieben
werden kann, so ist dies nicht zu empfehlen. Zum einen aus Gründen des
Datenschutzes und zum anderen wegen der Performance. Das ICC ist nicht
dafür optimiert, über ein Schuljahr hinweg zu arbeiten.

Folgende Entitäten sind Schuljahresabhängig:
* Lerngruppen und Unterrichte werden pro Abschnitt neu angelegt und existieren nur innerhalb
  eines Abschnittes.
* Lernende und Lehrkräfte existieren über Lernabschnitte hinweg. Basisdaten (Vorname, Nachname, E-Mail, ...)
  werden global gespeichert. Änderungen in den Basisdaten wirken sich somit auf alle Abschnitte aus.
* Klassen existieren über Lernabschnitte hinweg. Die Zuordnung der Klassenleitungen und die Mitgliedschaft der Lernenden
  wird pro Abschnitt erfasst.

## Klassenbuch

Die Klassenbuch-Funktion ist neu hinzugekommen. Funktionen:

* Anlegen von Klassenbucheinträgen (inkl. Änderungen im Stundenplan)
* Erfassen der Anwesenheit
* Markieren als Entfall
* Klassenbucheinträge
* Entschuldigungen
* Lernendenübersicht mit Fehlstunden und Klassenbucheinträgen
* Export als XML oder JSON

Folgende Funktionen sind geplant:
* PDF-Export zur Archivierung am Ende des Schuljahres
* ...

## Mitteilungen

Mitteilungen können nun Umfragen enthalten. Mehr dazu kann der Dokumentation entnommen werden.

## API

Die API hat sich verändert. Die neue API-Schnittstelle ist als OpenAPI-Spezifikation innerhalb des ICCs (Fußbereich -> API-Dokumentation)
zu finden.

## Administrative Aufgaben

Es ist nun möglich, einen Schuljahresabschnitt inkl. aller Daten (Unterrichte, Lerngruppen, Klassenbucheinträge, Stundenplan) 
zu löschen. Außerdem können nun alle Mitteilungen auf einen Schlag gelöscht werden.

Neu: Cronjobs für das optimieren der Datenbank und Löschen von verwaisten Lernenden.