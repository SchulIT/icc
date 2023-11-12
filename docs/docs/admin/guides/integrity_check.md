---
sidebar_position: 11
---

# Integritätscheck

Der Integritätscheck kann fehlerhafte Eingaben im Hinblick auf die Anwesenheitskontrolle überprüfen. Folgende Fehler
werden aktuell überprüft:

* Kind ist anwesend, obwohl es eine Abwesenheitsmeldung gibt
* Kind ist parallel in mehreren Unterrichten anwesend
* Anwesenheitsstatus wechsels mehr als einmal täglich

Diese Checks benötigen ca. eine halbe Sekunde pro Kind und sollten daher im Hintergrund laufen. Dies ist jedoch auf
konventionellen Web-Hostern nicht möglich.

## regelmäßiger Check im Hintergrund

Zunächst muss die Umgebungsvariable `ASYNC_CHECKS` auf `true` gesetzt werden in der `.env.local`.

Es gibt einen Cronjob, der nachts um 1 Uhr für alle Lernenden einen Check veranlasst. Damit diese Checks asynchron
abgearbeitet werden, wird der Symfony Messenger genutzt. Folgender systemd-Dienst muss dazu angelegt werden:

```
[Unit]
Description=Integritätscheck

[Service]
WorkingDirectory=/path/to/icc/
ExecStart=php /path/to/icc/bin/console messenger:consume checks --time-limit=3600 --memory-limit=256M
Restart=always
RestartSec=30

[Install]
WantedBy=default.target
```

Die Parameter `limit-limit` und `memory-limit` können entsprechend angepasst werden.