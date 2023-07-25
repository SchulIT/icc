---
sidebar_position: 1
---

# Begriffe

Das ICC modelliert den Schulalltag folgendermaßen.

## Lerngruppen und Unterrichte

Eine Lerngruppe ist eine Gruppe von Schülerinnen und Schülern. Diese Zusammensetzung ist entweder eine ganze Klasse oder
ein Kurs. Eine Lerngruppe kann aus einer beliebigen Zusammensetzung von Schülerinnen und Schülern bestehen. Beim Erstellen
wird angegeben, aus welchen Klassen die Lernenden in der Lerngruppe sind.

Ein Unterricht ist definiert als eine Zusammensetzung von Lerngruppe, Fach und Lehrkräften.

:::info Beispiel
Die Klassen 5A, ..., EF, Q1 und Q2 bilden jeweils eine Lerngruppe. 

Für Klassenunterrichte gilt dann zum Beispiel: Der Mathematikunterricht besteht aus dem Fach *Mathe*,
der Lerngruppe *5A* und der Lehrkraft *Mustermann*. Der Deutschunterricht besteht aus dem Fach *Deutsch*, der Lerngruppe
*5A* und der Lehrkraft *Musterfrau*. (Man sieht: Lerngruppen können wiederverwendet werden)

Für Kursunterrichte legt man in der Regel eine zum Kurs passende Lerngruppe an, zum Beispiel M-GK1 mit der Klasse EF. 
Der Unterricht setzt sich dann aus dem Fach *Mathe*, der Lerngruppe *M-GK1* sowie der Lehrkraft *Mustermann* zusammen.
:::

:::success Gewusst
Das Anlegen der Lerngruppen und Unterricht erfolgt automatisiert. Zum Beispiel mithilfe des [SchILD ICC Importer](https://schulit.de/software/schild-icc-importer)
:::

## Stundenplan

### Stundenplan-Stunde und -wochen

Jede Unterrichtsstunde besteht aus einem Raum, einem Fach, einem oder mehreren Lehrkräften und einer oder mehreren Klassen.
Sind alle Daten korrekt gepflegt, so kann anhand dieser Information die Stundenplanstunde einem Unterricht zugeordnet werden.

Grundsätzlich kennt das ICC beim Stundenplan keine periodischen Unterrichtsstunden. Stattdessen wird jede Unterrichtsstunde
separat (also pro Tag) abgespeichert. Die Anzeige von periodischen Wochen (z.B. A/B-Wochen) kann jeweils über die Kalenderwoche
des Tages angezeigt werden.

### Zuordnung des Stundenplans

Da für Lehrkräfte und Lernende die Zuordnung zu Unterrichten klar geregelt ist, entspricht der Stundenplan einer Lehrkraft
bzw. eines Schülers/einer Schülerin allen Stundenplanstunden, die den zugehörigen Unterrichten zugeordnet sind.

:::info Ausnahme
Kann einer Unterrichtsstunde beim Importieren kein Unterricht zugeordnet werden, so wird er jeweils der gesamten Klasse
zugeordnet.
:::

## Schuljahresabschnitte

Das ICC unterstützt Schuljahresabschnitte (z.B. Halbjahre). Der Zeitraum kann dabei beliebig gewählt werden, solange sich
die Zeiträume nicht überschneiden.

Grundsätzlich sind Lerngruppen und somit Unterrichte immer einem Schuljahresabschnitt zugeordnet. Das bedeutet auch, dass
obwohl sich eine Lerngruppe (oder deren Zusammensetzung) über das Schulhalbjahr nicht verändert, existiert die Lerngruppe
sowohl im ersten als auch im zweiten Halbjahr und wird vom System als verschiedene Lerngruppen behandelt. Analog gilt 
dies für Unterrichte.

Schülerinnen und Schüler sowie Lehrkräfte existieren jedoch nicht pro Abschnitt, sondern global. Sie werden den jeweiligen
Abschnitten zugeordnet, wenn sie darin "existieren". 