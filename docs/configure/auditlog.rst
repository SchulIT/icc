Audit-Log
=========

Mithilfe des Audit-Logs kann zurückverfolgt werden, welcher Benutzer welche Aktion durchgeführt hat, die eine Änderung
der Datenbank verursacht hat. Dabei werden dann das Erstellen, Bearbeiten und Löschen der verschiedenen Datenbank-Entitäten
(bspw. Mitteilung, Vertretungsplaneintrag, Dokument usw.) dokumentiert.

Gespeicherte Informationen
--------------------------

Bei jeder Datenbankaktion wird gespeichert:

- der Benutzer (hier wird der Originalbenutzer gespeichert, es lässt sich also auch zurückverfolgen, wenn sich jemand als jemand anderes ausgegeben hat)
- der Zeitpunkt
- die Art der Änderung
- die Änderungen im Detail (im Sinne von: vorher/nachher)
- die IP des Benutzers

Audit-Log anzeigen
------------------

Das Audit-Log befindet sich im System-Menü und ist für Benutzer mit der Rolle ``ROLE_SUPER_ADMIN`` einsehbar.