---
sidebar_position: 2
---

# Dashboard

Die Übersichtsseite implementiert eine Logik zur Anzeige der Tagesangenda. Diese Logik kann im Einstellungen-Menü :fa:`wrench`
unter Dashboard konfiguriert werden.

## Vertretungsplan

Es werden einige Parameter für die Verarbeitung des Vertretungsplans benötigt.

### Vertretungsarten mit geringer Priorität

Vertretungen mit dieser Art werden automatisch ausgeblendet, wenn es zu einer Unterrichtsstunde mehrere Vertretungen gibt.

Bei Nutzung von Untis sollten hier folgende Arten eintragen werden:

* Freisetzung
* Entfall

### Ergänzende Vertretungsarten

Bei manchen Vertretungen ist es notwendig, dass diese zusätzlich zu anderen Vertretungen oder der eigenen Unterrichtsstunde
angezeigt werden, bspw. bei Betreuungen. Diese können hier eingetragen werden. 

Bei der Nutzung von Untis sollten hier folgende Arten eingetragen werden:

* Betreuung
* Notbetreuung
* Veranstaltung

### Vertretungsarten Freistunden

Hier können Vertretungsarten angegeben werden, bei denen eine Stunde als Freistunde gewertet wird.

Bei der Nutzung von Untis sollten hier folgende Arten eingetragen werden:

* Freisetzung
* Entfall

### Nächster Tag ab

Hier kann die Uhrzeit festgelegt werden, ab der die Übersichtsseite automatisch auf den nächsten Tag wechselt (bspw. ab 16 Uhr).

### Wochenenden überspringen

Hier kann festgelegt werden, dass Wochenendtage in der Übersicht übersprungen werden (wenn man z.B. in der Navigationsleiste
auf den Pfeil für den nächsten Tag klickt).

### Anzahl der vergangenen/zukünftigen Tage

Über diese Parameter kann angegeben werden, wie viele Tage in der Navigationsleiste oberhalb der Übersichtsseite angezeigt
werden. 

:::tip Gewusst
Diese Parameter steuern nicht, welche Tage die Benutzer sehen können. Man kann grundsätzlich beliebig in die Zukunft bzw.
Vergangenheit reisen.
:::