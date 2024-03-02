# Private Nachrichten

Über die Funktion *Private Nachrichten* können - wie der Name erahnen lässt - private Nachrichten verschickt werden. 

### Aufbau

Private Nachrichten werden in Gesprächen organisiert (ähnlich wie bei einer E-Mail). Zu Beginn eines Gesprächs können
mehrere Teilnehmer hinzugefügt und ein Titel festgelegt werden.

Das System zeigt dem Absender einer Nachricht an, wer die Nachricht bereits gesehen hat.

Es können Anhänge von maximal 5 MB hochgeladen werden. Es dürfen PDF-Dateien sowie Bilder (PNG, JPG, JPEG) hochgeladen werden.
Anhänge können nur von der Person gelöscht werden, die sie hochgeladen hat.

### Integration Benachrichtigungssystem

Benutzer werden über das Benachrichtigungssystem über neue Nachrichten benachrichtigt.

### Aktivieren der Funktion

Die Funktion kann für beliebige Benutzergruppen über die Einstellungen freigeschaltet werden. Für jede Benutzergruppe
kann festgelegt werden, zu wem Gespräche begonnen werden können. In bereits begonnenen Gesprächen sind alle Teilnehmer
gleichberechtigt.

### Einschränkungen

Die Funktion ist noch nicht vollständig implementiert. So ist es noch nicht möglich, [Teilnehmer nach der Erstellung eines
Gesprächs hinzuzufügen oder zu löschen](https://github.com/SchulIT/icc/issues/442) oder [Nachrichten zu bearbeiten bzw. zu löschen](https://github.com/SchulIT/icc/issues/443).

### Alle Chats löschen

Zwischen den Schuljahren ist es sinnvoll, alle Chats aus dem System zu löschen. Dies ist jedoch bisher nur über die
Konsole möglich:

```bash
$ php bin/console app:chats:purge
```

