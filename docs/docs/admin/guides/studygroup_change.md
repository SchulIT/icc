---
sidebar_position: 10
---

# Lerngruppenwechsel

Wenn ein Schüler oder eine Schülerin die Lerngruppe wechselt (z.B. Klassenwechsel), so müssen folgende Punkte beachtet werden.

Der Klassenwechsel muss zunächst über das Schulverwaltungsprogramm durchgeführt werden und anschließend müssen sowohl die
Klassen- als auch die Lerngruppenmitgliedschaften aktualisiert werden. Dann ist der Schüler oder die Schülerin bereits
den richtigen Unterrichten zugeordnet.

Folgende Dinge sind nun korrekt:

* Klasse
* Unterrichte
* Stundenplan
* Vertretungsplan

## Klausurplan

Der Schüler oder die Schülerin ist noch den alten Klausuren zugeordnet, sodass es zu falschen Klausurplänen kommt. Daher
unter *Verwaltung ➜ Datenverwaltung ➜ Klausuren ➜ Lernenden neu zuordnen* den Schüler oder die Schülerin auswählen sowie das Datum des
Wechsels angeben. Das System ordnet die Klausuren (nach einer Bestätigung durch den Nutzer) neu zu.

:::tip Gut zu wissen
Diese Funktion benötigt die Rolle `ROLE_EXAMS_ADMIN`
:::

Anschließend sind auch die Klausuren korrekt zugeordnet.

## Unterrichtsbuch

Ab Version 2.3 wird der Lerngruppenwechsel im Unterrichtsbuch noch größenteils unterstützt. Siehe [GitHub](https://github.com/SchulIT/icc/issues/403)

Das Unterrichtsbuch arbeitet stets mit den aktuellen Listen. Um jedoch dennoch alte Unterrichte rekonstruieren zu können,
gibt es folgende Methoden:

Im Kontext von Unterrichten wird die Lernendenliste automatisch um jene Lernende erweitert, die mindestens einmal an dem Unterricht 
teilgenommen haben (d.h. für sie liegt eine An- oder Abwesenheit vor). Dies bezieht sich auch auf den Export.

Im Kontext von Klassen wird die Lernendenliste nicht automatisch erweitert. Es können jedoch sogenannte zeitliche Klassenmitgliedschaften
eingetragen werden. Dazu unter *Verwaltung ➜ Datenverwaltung ➜ EasyAdmin ➜ Klassen ➜ **bisherige Klasse des Lernenden auswählen** ➜ Zeitliche Mitgliedschaft*
entsprechend ergänzen. Dann werden die Lernenden auch in der Übersicht einer Klasse angezeigt (auch im Export). Die gezeigten
Zahlen beziehen sich dann jedoch nur auf den Kontext der Klasse (d.h. die angezeigten Fehlstunden gelten nur für die ausgewählte Klasse).
Für das Zeugnis müssen die Fehlstunden leider händisch zusammengerechnet werden, indem die Klassenübersicht der alten und neuen Klasse 
abgerufen werden.

Leider ist es aktuell (Version 2.3) nicht möglich, den alten Unterricht in der Anwesenheitsübersicht zu sehen (weder für
Lehrkräfte noch für Eltern). 