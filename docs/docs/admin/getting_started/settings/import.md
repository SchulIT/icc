---
sidebar_position: 10
---

# Import-Einstellungen

Hier können allgemeine Regeln für Importe festgelegt werden.

## Regeln für Klausurschreibende

Diese Regeln beziehen sich auf den Import des Klausurplans - entweder via API oder via Upload aus [Untis](../../import/untis).

Hier wird festgelegt, wann automatisch Teilnehmende eines Unterrichts als Klausurschreibende hinterlegt werden. Für
SchILD-NRW sollten folgende Zuordnungen festgelegt werden:

| Klassen | Abschnitt(e) | Kursart(en)         | Beschreibung                                                                                      |
|---------|--------------|---------------------|---------------------------------------------------------------------------------------------------|
| EF,Q1   | 1,2          | GKS,LK1,LK2,AB3,AB4 | EF: alle Teilnehmenden mit GKS / Q1: alle Teilnehmenden mit GKS oder Abifach (LK1, LK2, AB3, AB4) |
| Q2      | 1            | GKS,LK1,LK2,AB3,AB4 | Q2 - 1. Halbjahr: alle Teilnehmenden mit GKS oder Abifach (LK1, LK2, AB3, AB4)                    |
| Q2      | 2            | LK1,LK2,AB3         | Q2 - 2. Halbjahr: alle Teilnehmenden mit schriftlichen Abifach (LK1, LK2, AB3)                    |

:::tip Gewusst
Diese Zuordnung bezieht sich auf Gymnasien. Für Gesamt- oder Berufsschulen muss diese Zuordnung angepasst werden.
:::

### Fächer ohne Unterricht

Beim Import des Stundenplans werden alle Stunden automatisch Unterrichten zugeordnet. Das setzt jedoch voraus, dass diese
Unterricht auffindbar sind.

Es kann jedoch auch Fächer geben, denen kein Unterricht zugeordnet wird (z.B. Bereitschaften). Diese Fächer müssen hier
hinterlegt werden.

:::tip Gewusst
Der Name muss dem Fach aus dem entsprechenden Programm entsprechen, aus dem die Daten importiert werden (in der Regel
ist das [Untis](../../import/untis)).
:::

### Fallback-Schuljahresabschnitt

Beim Import des Terminplans für zukünftige Halbjahre kann es sein, dass diese noch gar nicht existieren oder dort
noch keine Klassen (bzw. Mitgliedschaften) hinterlegt sind.

Diese Option wird aktuell beim Import von Terminen verwendet. Wenn für zukünftige Schuljahresabschnitte noch keine 
Lerngruppendaten vorhanden, werden stattdessen die Lerngruppen im ausgewählten Abschnitt gesucht. 