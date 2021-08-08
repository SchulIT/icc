# Update von 1.3 auf 2.0

Wichtig: Dieses Update darf nicht im laufenden Schulbetrieb gemacht werden. 

Folgende Dinge werden gelöscht:

* Alle Mitteilungen (inkl. zugehöriger Dateien)
* Alle Zuordnungen der Dokumente zu Klassen
* Alle Termine
* Alle Klausuren
* Alle Vertretungen, Tagestexte, Absenzen
* Alle Krankmeldungen
* Alle Reservierungen
* Alle Klausuren
* kompletten Stundenplan (inkl. Perioden und Pausenaufsichten)

## Schritt 1: Datenbank sichern

Zunächst mittels `mysqldump` oder einem Datenbank-Tool wie HeidiSQL die Datenbank sichern.

## Schritt 2: Datenbank aufräumen

Folgende SQL-Befehle ausführen:

```sql
DELETE FROM appointment;
DELETE FROM exam;
DELETE FROM substitution;
DELETE FROM resource_reservation;
DELETE FROM sick_note;
DELETE FROM tuition;
DELETE FROM message;
DELETE FROM free_timespan;
DELETE FROM timetable_lesson;
DELETE FROM timetable_supervision;
DELETE FROM timetable_period;
DELETE FROM absence;
DELETE FROM infotext;
```

## Schritt 3: Code aktualisieren

Folgende Befehle auf der Kommandozeile ausführen:

```
$ rm -rf files/messages/*
$ git switch -b 2-0-stable
$ composer install --no-dev --optimize-autoloader
$ php bin/console cache:clear
$ php bin/console doctrine:migrations:migrate --no-interaction
$ php bin/console app:setup
$ php bin/console shapecode:cron:scan
```

## Schritt 4: Neuen Schuljahresabschnitt anlegen

Im Verwaltungs-Menü unter Abschnitte den ersten Schuljahresabschnitt anlegen. Wahlweise auch schon alle
Abschnitte anlegen.

**Wichtig:** Anschließend in den Einstellungen den aktuellen Abschnitt festlegen.

## Schritt 5: Import-Vorgang starten

Wichtig: Diesen Import-Vorgang mit Version 2.0 des Importers starten.

## Schritt 6: Schuljahresbeginn initiieren

Folgende Kommandos auf der Konsole ausführen:

```
$ php bin/console app:saml:remove_ids
$ php bin/console app:students:remove_orphaned
$ php bin/console app:user:remove_orphaned
$ php bin/console app:db:clear_audit
$ php bin/console app:db:optimize
```

Das ICC ist jetzt auf Version 2.0 und für das neue Schuljahr bereit.