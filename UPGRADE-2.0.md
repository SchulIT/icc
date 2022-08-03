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
# DELETE FROM appointment; # (optional)
DELETE FROM exam;
DELETE FROM substitution;
DELETE FROM resource_reservation;
DELETE FROM sick_note;
DELETE FROM message_poll_vote;
DELETE FROM message;
DELETE FROM free_timespan;
DELETE FROM absence;
DELETE FROM infotext;
DELETE FROM book_comment;
DELETE FROM lesson_attendance;
DELETE FROM lesson_entry;
DELETE FROM lesson;
DELETE FROM timetable_lesson;
DELETE FROM timetable_lesson_grades;
DELETE FROM timetable_lesson_teachers;
DELETE FROM timetable_period;
DELETE FROM tuition;
```

## Schritt 3: Mitteilungsdateien löschen

Da alle Mitteilungen gelöscht wurden, müssen nun noch die Dateien manuell gelöscht werden:

```
$ rm -rf files/messages/*
```

## Schritt 4: Code aktualisieren

Nun wie [hier](https://icc.readthedocs.io/de/latest/admin/update.html) beschrieben, den Code aktualisieren.

## Schritt 5: Konfigurationsdatei aktualisieren

Nun prüfen, inwiefern die `.env.local`-Konfigurationsdatei angepasst werden muss. Als Vorlage gilt die
`.env`-Konfigurationsdatei.

## Schritt 6: Neuen Schuljahresabschnitt anlegen

Im Verwaltungs-Menü unter Abschnitte den ersten Schuljahresabschnitt anlegen. Wahlweise auch schon alle
Abschnitte anlegen.

**Wichtig:** Anschließend in den Einstellungen den aktuellen Abschnitt festlegen.

## Schritt 7: Import-Vorgang starten

Wichtig: Diesen Import-Vorgang mit Version 2.0 des Importers starten.

## Schritt 8: Schuljahresbeginn initiieren

Folgende Kommandos auf der Konsole ausführen:

```
$ php bin/console app:saml:remove_ids
$ php bin/console app:students:remove_orphaned
$ php bin/console app:user:remove_orphaned
$ php bin/console app:db:clear_audit
$ php bin/console app:db:optimize
```

Das ICC ist jetzt auf Version 2.0 und für das neue Schuljahr bereit.