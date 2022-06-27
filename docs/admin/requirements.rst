Voraussetzungen
===============

Das ICC ist so programmiert, dass es auf gängigen Webspaces installiert werden kann.

Obligatorische Software
-----------------------

- Webserver (Apache oder nginx)
    - das ICC muss auf einer Subdomain laufen, also bspw. ``icc.example.com``
- PHP 8.0
    - aktivierte Plugins: json, ctype, iconv, openssl, xml, zip, dom
- MariaDB 10.4+
- SSH-Zugriff auf den Webspace
- Cronjobs (entweder als Skriptausführung oder HTTP-Anfrage)

Optionale Tools
---------------

Außerdem sollten folgende Tools auf dem Webserver installiert sein:

- git (wird zum Herunterladen des Codes benötigt)
- composer (wird zum Herunterladen von PHP Abhängigkeiten benötigt)
- nodejs und yarn (wird zum Kompilieren des Designs benötigt)

Falls die Tools nicht vorhanden sein sollten, kann man eine ZIP-Datei mit allen benötigten Dateien herunterladen und
auf dem Webspace hochladen.