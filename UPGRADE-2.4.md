# Upgrade von 2.3 auf 2.4

## Veränderungen unter Haube

* Das Projekt basiert nun auf Symfony 7x2
* Das Projekt benötigt nun PHP 8.3

## Wichtige Änderung
Die Versionierung von Dokumenten und Wiki-Artikeln musste entfernt werden, da die verwendete Bibliothek dafür nicht mit
der verwendeten ORM-Version kompatibel ist.

## Neue Rollen

Keine neuen Rollen.

## Gelöschte Parameter für Konfigurationsdatei

### NOTIFICATIONS_ENABLED

Das Benachrichtigungssystem kann nun in den Einstellungen unter dem Menüpunkt Benachrichtigungen ein- und ausgeschaltet
werden.

## Neue Parameter für Konfigurationsdatei

Keine neuen Parameter für die Konfigurationsdatei.

## Neue Features / Verbesserungen

Der zugrundeliegende Milestone 2.4 ist [auf GitHub](https://github.com/SchulIT/icc/milestone/14?closed=1) zu finden.

### Benachrichtigungssystem

Es ist nun möglich, für einzelne Ereignisse zu steuern, ob Personen aus der jeweiligen Zielgruppe immer, nie oder in Form
eines opt-in bzw. opt-out Verfahrens Benachrichtigungen per E-Mail bzw. Pushover erhalten. 

Benutzer haben die Möglichkeit, bei Opt-In bzw. Opt-Out Ereignissen entsprechend die Benachrichtigung ein- oder auszuschalten.

### Features aktivieren/deaktivieren

Es ist nun möglich, einzelne Funktionen des ICCs zu aktivieren bzw. zu deaktivieren (allerdings nicht alle). Sobald eine Funktion
deaktiviert ist, werden alle Menüeinträge ausgeblendet und entsprechende URLs sind nicht mehr erreichbar. 

Deaktivieren einer Funktion führt nicht zu Datenverlust. Alle bereits eingetragenen Daten bleiben in der Datenbank
erhalten und werden nur an den entsprechenden Stellen ausgeblendet.

### Benutzerabgleich mit Single Sign-On

Es ist nun möglich, dass im Hintergrund eine Synchronisation der Benutzer mit dem Single Sign-On erfolgt. Diese Synchronisation
wird zukünftig wichtig, wenn man per API auf das ICC zugreifen können soll, damit das ICC stets die aktuellen Daten hat.

Zur Aktivierung des Features bitte das [Handbuch](https://docs.schulit.de/icc/admin/guides/sso_sync) lesen.

### Allgemeine Verbesserungen & Bugfixes

## Upgrade TODO

Das Upgrade beinhaltet mehrere Migrationen.

**HINWEIS:** Dieses Update kann im laufenden Betrieb eingespielt werden. Es bedarf jedoch ggf. einer Anpassung der 
Konfigurationsdatei.

### Nach dem Upgrade

* Benachrichtigungsstrategien unter *Einstellungen ➜ Benachrichtigungen* festlegen. Anderenfalls werden keine Benachrichtigungen
  verschickt.
* Gewünschte Funktionen wieder unter *Einstellungen ➜ Features* aktivieren. Standardmäßig sind die Funktionen deaktiviert.