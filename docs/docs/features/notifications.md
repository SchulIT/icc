# Benachrichtigungen

Das ICC unterstützt sowohl Benachrichtigungen. Diese werden auf der Übersichtsseite bzw. oben rechts neben der Glocke
angezeigt. Benachrichtigungen können außerdem via E-Mail oder Pushover versendet werden.

Die Einstellungen sind zu finden unter *Verwaltung ➜ Einstellungen ➜ Benachrichtigungen*.

## Art von Benachrichtigungen

Das System verschickt bei folgenden Ereignissen Benachrichtigungen:

| Ereignis                                | Wer wird benachrichtigt?                                                                                       | obgligatorisch |
|-----------------------------------------|----------------------------------------------------------------------------------------------------------------|----------------|
| Neuer Vertretungsplan*                  | Alle Benutzer, die diese Option in den Profileinstellungen aktiviert haben.                                    | ❌️             |
| Neuer Klausurplan*                      | Alle Benutzer, die diese Option in den Profileinstellungen aktiviert haben.                                    | ❌              |
| Neue Mitteilung                         | Alle Benutzer, die diese Option in den Profileinstellungen aktiviert haben und Zielgruppe der Mitteilung sind. | ❌              |
| Neue Abwesenheitsmeldung                | Klassenleitung sowie alle zum Kind gehörenden Elternteile oder erwachsenen Kinder.                             | ✔️             |
| Neue Mitteilung zur Abwesenheitsmeldung | Ersteller der Meldung, Klassenleitung sowie alle zum Kind gehörenden Elternteile oder erwachsenen Kinder.      | ✔️             |
| Genehmigungsstatus wurde geändert       | Ersteller der Meldung, Klassenleitung sowie alle zum Kind gehörenden Elternteile oder erwachsenen Kinder.      | ✔️             |
| Abwesenheitsmeldung bearbeitet          | Ersteller der Meldung, Klassenleitung sowie alle zum Kind gehörenden Elternteile oder erwachsenen Kinder.      | ✔️             |
| Terminstatus geändert                   | Ersteller des Termins                                                                                          | ✔️             |

(*) Diese Ereignisse müssen in den Einstellungen für den Vertretungsplan bzw. den Klausurplan separat aktiviert werden.

## E-Mail Benachrichtigungen

Alle obligatorischen Benachrichtigungen werden auch automatisch via E-Mail versendet. Das setzt jedoch voraus, dass im System
für die Benutzer eine entsprechende E-Mail-Adresse hinterlegt ist.

:::tip Empfehlung
Es empfiehlt sich, die nicht-obgligatorischen E-Mails nur für eine Teilmenge der Benutzer (bspw. Lehrkräfte) freizugeben.
Alle oben als nicht-obgligatorisch markierten Ereignisse lösen ansonsten das Versenden von Massen-Mails (pro Benutzer)
eine E-Mail aus. Dies führt bei vielen E-Mail-Diensten (wie bspw. Microsoft 365) zu negativen Beeinträchtigungen. 
:::

## Pushover Benachrichtigungen

Als Alternative zum E-Mail-Versand von Benachrichtigungen bietet das ICC den Versand über den externen (und kostenpflichten)
Dienst [Pushover](https://www.pushover.net) an. Dieser eignet sich auch zum Versenden von Massen-Benachrichtigungen.

Eine Anleitung zur Konfiguration gibt es [hier](../admin/guides/pushover).