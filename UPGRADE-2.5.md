# Upgrade von 2.4 auf 2.5

## Veränderungen unter der Haube

* Das Projekt basiert nun auf Symfony 7.4
* Das Projekt benötigt nun mindestens PHP 8.4 (PHP 8.5 funktioniert ebenfalls)
* Das Projekt kann mittels FrankenPHP genutzt werden

## Wichtige Änderung

Die Verschlüsselung einzelner Datenbankspalten musste aufgrund von schlechter Performance im Produktivbetrieb abgeschaltet 
werden. 

## Neue Rollen

## Gelöschte Parameter für die Konfigurationsdatei

## Neue Parameter für die Konfigurationsdatei

## Veraltete Parameter für die Konfigurationsdatei

Der Parameter `DB_SECRET` wird fortan nicht mehr genutzt, da keine Verschlüsselung von Teilen der Datenbank erfolgt. 
**Achtung:** Der Schlüssel wird benötigt, um alle bisher verschlüsselten Spalten zu entschlüsseln. Er sollte daher erst 
nach der Entschlüsselung (siehe unten) aus der Konfigurationsdatei entfernt werden.

## Neue Features / Verbesserungen

### Lernendenübersicht

Die Lernendenübersicht wurde überarbeitet und in mehrere Tabs aufgeteilt. 

## Allgemeine Verbesserungen & Bugfixes

## Upgrade

Das Upgrade beinhaltet mehrere Migrationen. Vor oder nach dem Update müssen alle Datenbankspalten mit dem Befehl

```bash
$ php bin/console app:database:decrypt
```

entschlüsselt werden.

**HINWEIS:** Dieses Update kann im laufenden Betrieb eingespielt werden. Es empfiehlt sich jedoch ein Zeitpunkt, an dem
das ICC wenig genutzt wird (Nachts, Wochenende oder Ferien).

## Nach dem Upgrade


