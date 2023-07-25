# Auditierung

Mithilfe des Audit-Logs kann zurückverfolgt werden, welcher Benutzer welche Aktion durchgeführt hat, die eine Änderung
der Datenbank verursacht hat. Dabei werden dann das Erstellen, Bearbeiten und Löschen der verschiedenen Datenbank-Entitäten
(bspw. Mitteilung, Vertretungsplaneintrag, Dokument usw.) dokumentiert.

## Gespeicherte Informationen

Bei jeder Datenbankaktion wird gespeichert:

* der Benutzer (hier wird der Originalbenutzer gespeichert, es lässt sich also auch zurückverfolgen, wenn sich jemand als jemand anderes ausgegeben hat)
* der Zeitpunkt
* die Art der Änderung
* die Änderungen im Detail (im Sinne von: vorher/nachher)
* die IP des Benutzers

:::danger Achtung
Je nach Anzahl an Imports wird die Datenbank bei aktivierter Auditierung sehr groß (bei einem mittelgroßen Gymnasium über 1,5 GB).
:::

## Audit-Log anzeigen

Das Audit-Log befindet sich im Menü *Verwaltung* und ist für Benutzer mit der Rolle ``ROLE_SUPER_ADMIN`` einsehbar.

## Auditierung aktivieren/deaktivieren

Über die [Konfigurationsdatei](../install/configuration) `.env.local` kann gesteuert werden, ob die Auditierung aktiviert
ist oder nicht. Dazu muss der Parameter `AUDIT_ENABLED` entsprechend auf `true` oder `false` gesetzt werden.
