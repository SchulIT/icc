---
sidebar_position: 12
---

# Benutzerabgleich mit dem Single Sign-On

Ab Version 2.4 ist es möglich, Benutzerdaten im Hintergrund mit dem Single Sign-On abzugleichen. Dabei werden nur Benutzer
berücksichtigt, die sich bereits einmalig am ICC angemeldet haben.

Bei der Synchronisation werden alle (für das ICC relevanten) Attribute vom Single Sign-On abgefragt und anschließend in
der ICC-Datenbank aktualisiert.

## Zeitplan

Der Abgleich passiert per Cronjob einmal täglich um Mitternacht.

## Manueller Abgleich

Um den Abgleich manuell anzustoßen, muss das folgende Kommando ausgeführt werden:

```bash
$ php bin/console app:users:update
```

Dies stößt den asynchronen Abgleichprozess an. Er kann über *Verwaltung ➜ Messenger* überwacht werden.

## Aktivierung

Um die Funktion zu aktivieren, muss die Konfigurationsvariable `SSO_USER_UPDATE` auf `true` gesetzt werden. Außerdem
müssen die Variablen `SSO_URL` und `SSO_APITOKEN` entsprechend gesetzt sein. Details zu den Konfigurationsparametern
bitte [hier](../install/configuration#SSO_USER_UPDATE) entnehmen

Damit der Abgleich im Hintergrund stattfinden kann, muss folgender systemd-Dienst angelegt werden:

```
[Unit]
Description=ICC Benutzerabgleich mit SSO

[Service]
WorkingDirectory=/path/to/icc/
ExecStart=php /path/to/icc/bin/console messenger:consume users --time-limit=3600 --memory-limit=256M
Restart=always
RestartSec=30

[Install]
WantedBy=default.target
```

Die Parameter `limit-limit` und `memory-limit` können entsprechend angepasst werden.