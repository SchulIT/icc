# Klausurplanung

Benutzer mit der [Rolle](../admin/roles) `ROLE_EXAM_ADMIN` können Klausuren auf dem ICC anlegen. Diese können unter *Verwaltung ➜
Datenverwaltung ➜ Klausuren* verwaltet werden.

## Zentrale Klausurplanung

Erfolgt die Planung zentral, so kann über *Neue Klausur* eine neue Klausur erstellt werden mit den jeweiligen Daten (Datum,
Unterricht(e), Klausurschreibende etc.).

:::tip Gewusst
Idealerweise plant man die Klausuren mit externen Tools und importiert sie anschließend, bspw. über einen Zwischenschritt
über [Untis](../admin/import/untis).
:::

## Klausurplanung durch Fachlehrkräfte

Möchte man die Planung der Klausuren den Fachlehrkräften überlassen (das wird häufig in der Sekundarstufe I so gehandhabt),
so klickt man auf *Mehrere Klausuren erstellen* und gibt die Anzahl der Klausuren sowie die entsprechenden Unterrichte an,
für die Klausuren erstellt werden. Die Option *Alle Lernenden der ausgewählten Unterrichte als Klausurschreiber zuordnen*
sollte aktiviert sein, sonst muss dies später händisch gemacht werden.

Das Anlegen sollte in der Regel über die jeweiligen Stufenkoordinatoren erfolgen (bspw. Mittelstufenkoordination).

Dabei sollte beachtet werden, dass für jeden ausgewählten Unterricht die entsprechende Anzahl an Klausuren erstellt wird.
Datum, Stunden und Ort sind dabei nicht belegt. Die Fachlehrkraft hat anschließend über die *Klausurübersicht* die Möglichkeit,
die Klausuren einem Datum zuzuordnen.

:::tip Gewusst
Bei dieser Praxis benötigen die Lehrkräfte die Rolle `ROLE_EXAM_ADMIN` **nicht**.
:::

:::tip Gewusst
Klausuren ohne Datum und Stunden werden nicht auf dem Klausurplan angezeigt. Sie sind also auch für Lernende nicht sichtbar.
:::

:::tip Gewusst
Es werden immer nur Unterrichte des **aktuellen Abschnittes** angezeigt (dieser kann in den [Einstellungen](../admin/getting_started/sections))
festgelegt werden).
:::

:::warning Wichtig
Immer nur so viele Klausuren erstellen, wie auch tatsächlich pro Halbjahr durchgeführt werden können.
:::