---
sidebar_position: 31
---

# Cronjobs

Das System muss einige wiederkehrende Aufgaben ausführen, bspw. das automatische "Entfallenlassen" von Unterrichtsstunden,
sofern dies durch den Kalender gegeben ist.

:::caution Hinweis
Damit diese Funktion funktioniert, muss zusätzlich ein Hintergrundprozess laufen, der [hier](./background_jobs) beschrieben ist.
:::

## systemd-Dienst für Cronjobs

Ein entsprechender systemd-Prozess sieht folgendermaßen aus (`~/.config/systemd/user/icc-cron.service`):

```
[Unit]
Description=ICC Cronjobs

[Service]
WorkingDirectory=/path/to/icc/
ExecStart=/usr/bin/php /path/to/icc/bin/console messenger:consume scheduler_default --time-limit=3600 --memory-limit=256M
Restart=always
RestartSec=30

[Install]
WantedBy=default.target
```
