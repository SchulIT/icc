Schuljahresabschnitte
=====================

Auf dem ICC lassen sich Halbjahre oder Semester in Form von Schuljahresabschnitten abbilden. Ein Datenimport aus SchILD
erfolgt stets in einen bestimmten Abschnitt. Die Stundenplanperioden orientieren sich an den Schuljahresabschnitten. Dabei
ist jedoch zu beachten, dass Stundenplanperioden nicht über die Grenzen eines Schuljahresabschnitt hinaus definiert sein
können (eine Periode ist somit immer genau einem Abschnitt zugeordnet).

Folgende Entitäten sind abhängig vom Abschnitt:

* Lerngruppen und Unterrichte sind jeweils genau einem Abschnitt zugeordnet. Unterrichte, die über ein Schuljahr gehen,
  werden somit für jeden Abschnitt separat im ICC verwaltet (und auch automatisch so importiert).
* Lernende und Lehrkräfte exitieren über Abschnitte hinweg. Die Basisdaten (Vorname, Nachname, E-Mail, ...) werden dabei
  unabhängig vom Abschnitt gespeichert. Änderungen in den Basisdaten wirken sich somit auf alle Abschnitte aus. Ändert sich
  bspw. das Kürzel einer Lehrkraft, so muss diese Änderung auch rückwirkend in allen Daten (bspw. in Untis) erfolgen, da
  es anderenfalls Schwierigkeiten bei der Erkennung der Lehrkräfte gibt.
* Jedem Lernenden und jeder Lehrkraft können mehreren Abschnitten zugeordnet sein.
* Damit Lernende und Lehrkräfte als "aktiv" vom System erkannt werden, müssen sie dem jeweils aktuellen Abschnitt zugeordnet sein.
  Anderenfalls ist eine Anmeldung am ICC nicht möglich.
* Klassen existieren über Lernabschnitte hinweg. Die Zuordnung der Klassenleitungen und Lernenden erfolgt dabei pro Abschnitt.

Wichtiger Hinweis zu Mitteilungen
---------------------------------

Die Auswahl der Lerngruppen einer Mitteilung erfolgt stets in Abhängigkeit von der aktuellen Periode. Dies ist gerade
um den Wechsel von einem Abschnitt in den nächsten Abschnitt problematisch, da sich die Lerngruppen von Abschnitt zu Abschnitt
unterscheiden.

Es ist daher ratsam, die Lehrkräfte zu informieren, dass kurz vor dem Wechsel keine Mitteilungen für den neuen Abschnitt
erstellt werden sollten.

Verwalten von Abschnitten
#########################

Das Verwalten von Abschnitten wird im Verwaltungsmenü :fa:`cogs` unter Abschnitte erledigt. Dort können Abschnitte erstellt,
bearbeitet oder gelöscht werden.

.. danger:: Nach dem Erstellen des ersten Abschnittes muss dieser als aktiv markiert werden. Dies geschieht in den
   Einstellungen :fa:`wrench` unter Abschnitte. Auch wenn der Abschnitt ggf. vorausgewählt ist, muss dies mit "Speichern" bestätigt werden.

Löschen von Abschnitten
#######################

Beim Löschen von Abschnitten werden alle zugehörigen Datensätze gelöscht. Dies betrifft unter anderem Unterrichte, Lerngruppen,
Lernende- und Lerhkraftzuordnungen sowie alle Einträge in den Unterrichtsbüchern.

