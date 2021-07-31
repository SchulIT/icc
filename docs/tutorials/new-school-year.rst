Schuljahreswechsel
==================

Zum Schuljahreswechsel sollte das ICC aufgeräumt und weitestgehend zurückgesetzt werden, sodass das neue Schuljahr
ohne Altlasten beginnen kann.

Altlasten entfernen
###################

1) Im Verwaltungs-Menü :fa:`cogs` unter :fa:`envelope-open-text` Mitteilungen alle alten Mitteilungen entfernen
2) Im Verwaltungs-Menü :fa:`cogs` unter :fa:`calendar` Termine alle Termine entfernen (dieser Schritt ist optional)
3) Im Verwaltungs-Menü :fa:`cogs` unter :fa:`sliders-h` Abschnitte alle Abschnitte entfernen. Mit diesem Schritt
   werden auch alle Unterrichte, Lerngruppen, Klassen- und Lerngruppenzugehörigkeiten, Klassenleitungen sowie die Zuordnungen
   von Lernenden und Lehrkräften zu den Abschnitten entfernt.

Datenbank aufräumen
###################

Im System-Menü :fa:`tools` unter :fa:`history` Cronjobs die folgenden Cronjobs ausführen:

1) app:saml:remove_ids
2) app:students:remove_orphaned
3) app:user:remove_orphaned
4) app:db:optimize

Abschnitt anlegen und Import starten
####################################

Im Verwaltungs-Menü :fa:`cogs` unter :fa:`sliders-h` den ersten Schuljahresabschnitt anlegen. Wahlweise auch schon alle
Abschnitte anlegen.

**Wichtig:** Anschließend in den Einstellungen :fa:`wrench` den aktuellen Abschnitt festlegen.

Import starten
##############

Nun den Import für den ersten Abschnitt starten. Zunächst alle Plandaten aus der Schulverwaltung importieren. Wenn der
Import aus SchILD heraus erfolgt, geht es `hier <../import/schild-nrw.html>`_ weiter. Erst im Anschluss Stunden-, Vertretungs-,
Klausur- und/oder Terminplan importieren. Wenn der Import aus Untis geschieht, geht es `hier <../import/untis.html>`_ weiter.


