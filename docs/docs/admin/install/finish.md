---
sidebar_position: 5
---

# Installation abschließen

Nun die folgenden Kommandos ausführen, um die Installation abzuschließen:

## CSS- und JavaScript-Dateien erzeugen

```bash
$ php bin/console bazinga:js-translation:dump assets/js/ --merge-domains
$ npm run build
$ php bin/console assets:install
```

## Abschließende Kommandos

```bash
# Cache leeren und aufwärmen
$ php bin/console cache:clear
# Datenbank erstellen
$ php bin/console doctrine:migrations:migrate --no-interaction
# Anwendung installieren
$ php bin/console app:setup
# Cronjobs registrieren
$ php bin/console shapecode:cron:scan
```

## Webserver konfigurieren

Die Konfiguration des Webservers kann in der [Symfony Dokumentation](https://symfony.com/doc/current/setup/web_server_configuration.html)
nachgelesen werden.

:::danger Wichtig
Es ist wichtig, dass das `public/`-Verzeichnis als Wurzelverzeichnis im Webserver hinterlegt ist. Anderenfalls können 
Konfigurationsdateien abgefangen werden.
:::

## Cronjobs konfigurieren

Siehe [Cronjobs](../maintenance/cronjobs).