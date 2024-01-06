---
sidebar_position: 30
---

# Cronjobs

Das System muss einige wiederkehrende Aufgaben ausführen, bspw. das Versenden von Benachrichtigungen. Der Administrator
muss daher sicherstellen, dass diese Aufgaben automatisiert ausgeführt werden.

## Cronjobs mittels HTTPS-Anfrage ausführen

Viele Webspace-Betreiber bieten die Möglichkeit, Cronjobs in Form von HTTP-Anfragen zu realisieren. Dazu wird dann in
einem fest definierten Zeitintervall eine Website vom System aufgerufen.

### Schritt 1: Passwort festlegen

Zunächst muss das Passwort für den Cronjob-Benutzer `cron` generiert werden.

:::info
Dieser Benutzer existiert nicht in der Benutzerdatenbank.
:::

Im folgenden Schnipsel muss `PASSWORD` durch das gewünschte Passwort ersetzt werden. Es empfiehlt sich, das Passwort
generieren zu lassen (z.B. von einem Passwortmanager oder [random.org](https://random.org)).

```bash
$ php bin/console security:encode-password --no-interaction PASSWORD Symfony\Component\Security\Core\User\User

 ------------------ ---------------------------------------------------------------------------------------------------
  Key                Value
 ------------------ ---------------------------------------------------------------------------------------------------
  Encoder used       Symfony\Component\Security\Core\Encoder\MigratingPasswordEncoder
  Encoded password   $argon2id$v=19$m=65536,t=4,p=1$YXNrRzRXZGZwdi51S202eQ$DlMW6D+P896CMTj1U/Jn7KssfJqLcU98Q+lIm+AVOmk
 ------------------ ---------------------------------------------------------------------------------------------------
```

In der Ausgabe ist das Passwort enthalten. Der Wert von `Encoded password` muss in der Konfigurationsdatei `.env.local`
unter `CRON_PASSWORD` eingetragen werden (Anführungszeichen nicht vergessen).

:::info
Der Wert des Passwortes ändert sich mit jedem Aufruf des Kommandos, auch wenn das Passwort identisch ist.
:::

### Schritt 2: Cronjob einrichten

Folgende Daten nun in der Crobjob-Verwaltung des Webhosters festlegen:

* URL: `https://icc.schulit.de/cron` (dabei natürlich `icc.schulit.de` durch die echte Adresse ersetzen)
* Intervall: alle zwei Minuten
* Authentifizierung:
    * Art: HTTP Basic
    * Benutzername: cron
    * Passwort: *wurde in Schritt 1 festgelegt*

## Cronjobs auf Betriebssystemebene ausführen

Falls man einen eigenen Server betreibt, können Cronjob-Programme wie crontab oder systemd genutzt werden, um ein PHP-Skript
in regelmäßigen Abständen auszuführen. Dazu im Cronjob-Programm folgendes Skript alle zwei Minuten ausführen lassen:

```bash
/usr/bin/php /path/to/icc/bin/console shapecode:cron:run
```

:::caution Achtung
`/usr/bin/php` muss ggf. durch den Pfad zur PHP Executable ersetzt werden (z.B. wenn mehrere PHP-Versionen auf dem System vorhanden sind)
:::