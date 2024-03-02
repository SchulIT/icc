---
sidebar_position: 40
---

# Schuljahreswechsel

Zum Schuljahreswechsel sollte das ICC aufgeräumt und weitestgehend zurückgesetzt werden, sodass das neue Schuljahr
ohne Altlasten beginnen kann.

## Altlasten entfernen

1. Unter *Verwaltung ➜ Datenverwaltung ➜ Mitteilungen* alle alten Mitteilungen entfernen.
2. Unter *Verwaltung ➜ Datenverwaltung ➜ Abschnitte* alle Abschnitte aus dem Schuljahr entfernen. Mit diesem Schritt
werden auch alle Unterrichte, Lerngruppen etc. entfernt.
3. Alle Chats aus dem System löschen. Dazu muss `php bin/console app:chats:purge` ausgeführt werden. Aktuell kann diese
Aktion nicht mittels Browser durchgeführt werden.

## Neues Schuljahr anlegen

Unter *Verwaltung ➜ Datenverwaltung ➜ Abschnitte* alle Abschnitte für das neue Schuljahr anlegen.

## Stammdaten importieren

Nun die Stammdaten (z.B. aus SchILD NRW) importieren. Siehe [Importvorgang](../import/schild).

## Datenbank aufräumen

Über eine SSH-Konsole folgende Kommandos ausführen:

```bash
$ # Alle alten SAML Response IDs verwerfen
$ php bin/console app:saml:remove_ids
$ # Alle Lernenden löschen, denen kein Abschnitt zugeordnet ist
$ php bin/console app:students:remove_orphaned
$ # Alle Benutzer löschen, denen kein Schüler/keine Schülerin bzw. Lehrkraft zugeordnet ist
$ php bin/console app:user:remove_orphaned
$ # Das Audit-Log leeren
$ php bin/console app:clear-audit
$ # Datenbanktabellen optimieren
$ php bin/console app:db:optimize
```

## Plandaten importieren

Nun können auch die Plandaten wie Stundenplan importiert werden.