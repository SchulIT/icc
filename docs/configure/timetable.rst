Stundenplan
===========

Zunächst sollte der Stundenplan an die eigene Schule angepasst werden. Dazu zählen unter anderem die Angabe der
Stundenanzahl, Unterrichtszeiten und Pausen. Die Angabe der Stundenplan-Perioden sowie Wochenperiodizität ist ebenfalls
notwendig.

Diese Einstellungen lassen sich im Einstellungen-Menü :fa:`wrench` unter Stundenplan vornehmen. Stundenplan-Perioden und
-wochen lassen sich im Verwaltungs-Menü :fa:`cogs` unter Stundenplan verwalten.

Allgemeine Informationen
------------------------

Anzahl der Unterrichtsstunden
#############################

Hiermit wird die Anzahl der Unterrichtsstunden festgelegt.

.. warning:: Erst nachdem die Anzahl festgelegt wurde, können die Unterrichtszeiten angegeben werden.

Kategorien für Unterrichtsfrei
##############################

Wenn in den angegebenen Kategorien ein Termin an einem Tag vorhanden ist, wird dieser Tag als Unterrichtsfrei angezeigt
und der Stundenplan auf dem Dashboard ist leer. Außerdem wird beim Kalender-Export des Stundenplans an diesem Tag kein
Stundenplantermin eingefügt und der Tag erscheint als leer.

Klassen mit Kursbezeichnungen
#############################

Bei diesen Klassen wird anstatt des Faches die Kursbezeichnung im Stundenplan angezeigt.

Klassen mit Kursarten
#####################

Bei diesen Klassen wird zusätzlich die Kursart im Stundenplan von Lernenden angezeigt.

Unterrichtsstunden
------------------

Hier werden die Unterrichtszeiten konfiguriert. Dabei muss für jede Stunde ein Beginn und ein Ende angegeben werden.
In einem Doppelstundensystem, wo die Stunden nicht durch eine Pause getrennt sind, können Ende der ersten und Beginn
der zweiten Stunde identisch sein.

Die Option "N. Stunde kann mit voriger Stunde zu einer Doppelstunde zusammengefasst werden" sollte immer dann aktiviert
werden, wenn es Doppelstunden geben kann.

Beispiel: 1./2., 3./4, 5./6., 8./9. Stunde können (müssen aber nicht) jeweils Doppelstunden sein, dann wird das Häckchen
jeweils bei der 2., 4., 6. und 9. Stunde gesetzt.

.. warning:: Wenn diese Option nicht gesetzt wird, werden die Stunden immer als Einzelstunden im Stundenplan angezeigt.

Aufsicht
--------

Hier können die Aufsichtstexte konfiguriert werden. Zusätzlich werden hier auch die Anzeigetexte der Pausen definiert,
da diese für die Pausenaufsichten benötigt werden.

Mit der Angabe einer Farbe kann des Feld der Pausenaufsicht entsprechend hinterlegt werden (optional).

Stundenplan-Perioden und -wochen
--------------------------------

Perioden
########

Grundsätzlich muss der Stundenplan in Perioden angegeben werden. Die Anzahl an Perioden ist dabei unerheblich. Hat man
eigentlich keine Stundenplan-Perioden, so gibt man eine Periode an, die über das gesamte Schuljahr verläuft.

Jede Periode kann individuell für einzelne Benutzergruppen freigeschaltet werden. Die Externe ID ist jene ID, die der
Untis Importer mitschicken muss, wenn er Stundenplaneinträge für eine Periode importieren möchte.

.. danger:: Wenn Perioden durch den Untis ICC Importer angelegt werden, müssen diese anschließend für die gewünschten
   Benutzergruppen freigeschaltet werden. Standardmäßig sind sie für niemanden sichtbar.

Wochen
######

Viele Schulen nutzen A- und B-Wochen, um den Stundenplan abzubilden. Wie bspw. in Untis muss auch dies im ICC konfiguriert werden.
Aktuell kann das ICC solche Wochen nur anhand der Kalenderwoche erkennen, d.h. ungerade Wochen (Wochen-Modulo 1) können
der A-Woche und gerade Wochen (Wochen-Modulo 0) der B-Woche zugeordnet werden. Bei einer C-Woche würde man den Modulo 2 noch ergänzen.

Der Schlüssel der Woche ist dabei eine Zeichenkette, die das System nutzt, um Stundenplaneinträge beim Import der
richtigen Woche zuzuordnen.

**Tipp:** Standard A- und B-Wochen können per Knopfdruck angelegt werden.

