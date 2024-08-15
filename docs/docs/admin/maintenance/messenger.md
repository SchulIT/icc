---
sidebar_position: 31
---

# Hintergrundaufgaben

Einige Aufgaben wie bspw. der E-Mail-Versand werden asynchron im Hintergrund ausgeführt. Dazu wird der [Symfony Messenger](https://symfony.com/components/messenger)
versendet. Dieser wird standardmäßig als Hintergrunddienst über einen Supervisor (bspw. systemd unter Linux) ausgeführt.
Das setzt jedoch voraus, dass man Zugriff auf diesen hat. Bei Webhostern ist dies klassischerweise nicht der Fall.

Das System kann daher so konfiguriert werden, dass diese Hintergrundaufgaben als Cronjob ausgeführt werden.

## Konfiguration als Cronjob (Webhoster)

Dazu muss in der Konfigurationsdatei `.env.local` der Parameter `MESSENGER_CRONJOB=true` gesetzt werden. 

:::warning Hinweis
Sobald der Parameter geändert wurde, müssen folgende Kommandos ausgeführt werden:

```bash
$ php bin/console cache:clear
$ php bin/console shapecode:cron:scan
```

Mit dem letzten Kommando werden zwei Cronjobs registriert (einmal für den E-Mail-Versand und einmal für allgemeine Hintergrundaufgaben).
:::

:::tip Gut zu wissen
Die beiden Cronjobs werden einmal pro Minute ausgeführt und haben eine maximale Laufzeit von 20 Sekunden. In der Regel ist das
ausreichend. Leider lässt sich dies (Stand Version 2.3) nicht konfigurieren.
:::

## Konfiguration als systemd-Dienst

Hat man einen eigenen Server, so ist in der Konfigurationsdatei `.env.local` der Parameter `MESSENGER_CRONJOB=false` zu setzen.

:::warning Hinweis
Sobald der Parameter geändert wurde, müssen folgende Kommandos ausgeführt werden:

```bash
$ php bin/console cache:clear
$ php bin/console shapecode:cron:scan
```

Mit dem letzten Kommando werden die beiden Cronjobs für Hintergrundaufgaben (sofern sie bisher existierten) gelöscht.
:::

Anschließend muss noch zwei entsprechender systemd-Dienst installiert werden (siehe [offizielle Dokumentation](https://symfony.com/doc/current/messenger.html#systemd-configuration)).

Zunächst der Mail-Dienst `icc-mail.service`:

```
[Unit]
Description=ICC Mails

[Service]
ExecStart=/usr/bin/php /path/to/icc/bin/console messenger:consume mail --time-limit=3600
Restart=always
RestartSec=30

[Install]
WantedBy=default.target
```

Der Dienst für allgemeine Hintergrundaufgaben `icc-background.service`:

```
[Unit]
Description=ICC Hintergrundaufgaben

[Service]
ExecStart=/usr/bin/php /path/to/icc/bin/console messenger:consume async --time-limit=3600
Restart=always
RestartSec=30

[Install]
WantedBy=default.target
```

:::tip Tipp
Es wird empfohlen, den Dienst als sogenannten *user service* laufen zu lassen.
:::