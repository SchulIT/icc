---
sidebar_position: 1
---

# Voraussetzungen

Grundsätzlich muss SSH-Zugriff auf den Server vorhanden sein. 

## Single Sign-On

Da das ICC selbst keine Benutzerverwaltung besitzt, werden Benutzer über das [SchulIT Single Sign-On](https://schulit.de/software/idp)
angemeldet. Es wird daher eine fertig konfigurierte Instanz des Single Sign-Ons benötigt.

:::tip Gewusst
Dank SAML ist es nicht notwendig, dass das Single Sign-On und das ICC auf demselben Server betrieben werden. Es kann
aber selbstverständlich auch auf demselben Server betrieben werden.
:::

## Software
* Webserver
  * Apache 2.4+ oder
  * nginx
* PHP 8.3+ mit folgenden Erweiterungen
  * ctype
  * curl
  * dom
  * fileinfo
  * iconv
  * json
  * libxml
  * mbstring
  * openssl
  * pdo_mysql
  * phar
  * simplexml
  * tokenizer
  * xml
  * xmlwriter
  * xsl
* MariaDB 10.4+ (ein kompatibles MySQL kann funktionieren, ist jedoch nicht getestet)
* Composer 2+
* Git (zum Einspielen des Quelltextes)
* NodeJS >= 18 inkl. NPM (zum Erstellen der JavaScript- und CSS-Dateien)

Die Software muss auf einer Subdomain betrieben werden. Das Betreiben in einem Unterverzeichnis wird nicht unterstützt.

:::tip Hinweis
Theoretisch ist es auch ohne Git und NodeJS möglich, die Software zu installieren. Dazu kann der Quelltext mittels GitHub
heruntergeladen werden. Die Assets müssen dann jedoch auf einer Maschine erzeugt werden, wo Node und NPM verfügbar sind.
Dann muss das gesamte `/public/build`-Verzeichnis nach dem Erstellen der Assets auf den Webspace kopiert werden.
:::

### Empfohle Software (optional)

* PHP acpu-Erweiterung

## Untis

Wird Untis zum Import der Planungsdaten verwendet, so wird das Info-Stundenplan-Modul benötigt.

## Hardware

An die Hardware stellt das System keine besonderen Anforderungen. Allerdings kann die Datenbank groß - in Abhängigkeit
von der Größe der Schule (Anzahl Lehrkräfte bzw. Schülerinnen und Schüler) - groß werden. Hier ist auch relevant, ob
eine Auditierung aktiv oder nicht. Bei aktiver Auditierung benötigt die Software bei einem Gymnasium mit ca. 800 Schülerinnen
und Schülern sowie 70 Lehrkräften ca. 1,5GB pro Schuljahr.

## Installation auf Webspaces

Grundsätzlich ist es möglich, das ICC auf einem konventionellen Webspace zu betreiben (sofern die obigen Anforderungen
erfüllt sind). Es ist aber abhängig vom Anbieter und dem entsprechenden Paket, ob das ICC einwandfrei funktioniert.