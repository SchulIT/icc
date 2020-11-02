Konfiguration
=============

Konfigurationsdatei anlegen
---------------------------

Die Vorlage für die Konfigurationsdatei befindet sich in der Datei ``.env``. Von dieser Datei muss eine Kopie ``.env.local`` erzeugt werden.
Anschließend muss die Datei angepasst werden.

.. code-block:: shell

    $ cp .env .env.local

Konfigurationseinstellungen
---------------------------

APP_ENV
#######

Dieser Wert muss immer ``prod`` enthalten, sodass das System in der Produktionsumgebung ist.

.. warning:: Niemals ``dev`` in einer Produktivumgebung verwenden.

APP_SECRET
##########

Dieser Wert muss eine zufällige Zeichenfolge beinhalten. Diese kann beispielsweise mit ``openssl rand -base64 32`` erzeugt werden

APP_URL
#######

Dieser Wert beinhaltet die URL zur ICC-Instanz, bspw. https://icc.example.com/

APP_NAME
########

Name des ICCs, kann nach Belieben geändert werden.

APP_LOGO
########

Pfad zum großen Logo für den Fußbereich. Das Bild muss im ``public``-Ordner (oder einem Unterordner) abgelegt werden.

APP_SMALLLOGO
#############

Pfad zum kleinen Logo für den Kopfbereich. Das Bild muss im ``public``-Ordner (oder einem Unterordner) abgelegt werden.

SAML_ENTITY_ID
##############

ID des ICCs, welche für SAML-Anfragen (Authentifizierung) genutzt wird. Dieser Wert muss mit dem Wert im Identity Provider übereinstimmen.
Als Entity ID wird in der Regel die URL der Anwendung (bspw. ``https://icc.example.com/`` verwendet).

IDP_PROFILE_URL
###############

Link zu den Kontoeinstellungen im Identity Provider, bspw. ``https://sso.schulit.de/profile``.

IDP_LOGOUT_URL
##############

Link zur Abmeldung vom Identity Provider, bspw. ``https://sso.schulit.de/logout``.

MAILER_FROM
###########

E-Mail-Adresse des Absenders von E-Mails aus der Anwendung heraus, bspw. ``noreply@schulit.de``.

MAILER_LIMIT
############

Bei einigen Anbietern kann man nur eine bestimmte Anzahl an E-Mails pro Minute versenden (bspw. bei Office 365). Diese Anzahl
hier eintragen. Bei unbegrenzter Anzahl kann ein hoher Wert (bspw. ``99999``) angegeben werden.

CRON_PASSWORD
#############

Das Passwort für den Cronjob-Benutzer, welcher Cronjobs über eine HTTP-Anfrage ausführt. Siehe Cronjobs.

IMPORT_PSK
##########

Pre-Shared-Key für alle Importe in das ICC. Siehe Import.

LANGUAGE
########

Legt die Sprache des ICC fest. Aktuell kann nur ``DE`` ausgewählt werden.

NOTIFICATIONS_ENABLED
#####################

Legt fest, ob das ICC grundsätzlich Benachrichtigungen versendet oder nicht. Einzelne Benachrichtigungsarten können später über das
Web-Interface festgelegt werden. Sollte standardmäßig ``true`` sein und dient dazu, Benachrichtigungen global zu deaktivieren (``false``).

AUDIT_ENABLED
#############

Legt fest, ob das Audit-Logging aktiv (``true``) oder nicht aktiv (``false``) ist. Ist es aktiv wird jede Datenbankaktion (Anlegen, Bearbeiten und Löschen
von Mitteilungen, Vertretungen etc.) in der Datenbank gespeichert und ist einem Benutzer zuzuordnen.

DATABASE_URL
############

Verbindungszeichenfolge für die Datenbankverbindung. Aktuell unterstützt das ICC ausschließlich MySQL/MariaDB-Datenbanken
ab Version MySQl 5.7. Die Zeichenfolge setzt sich dabei folgendermaßen zusammen:

.. code-block:: shell

    mysql://USERNAME:PASSWORD@HOST:3306/NAME

- ``USERNAME``: Benutzername der Datenbank
- ``PASSWORD``: zugehöriges Passwort des Datenbankbenutzers
- ``HOST``: Hostname des Datenbankservers
- ``NAME``: Name der Datenbank

Weitere Informationen (englisch) gibt `hier <https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url>`_.

MAILER_URL
##########

Verbindungszeichenfolge für das E-Mail-Postfach, welches zum Versand von E-Mails verwendet werden soll. Beispiele:

- Generischer SMTP-Versand: ``smtp://SMTPSERVER:465?encryption=ssl&auth_mode=login&username=USERNAME&password=PASSWORD``
- Google Mail-Postfach: ``gmail://USERNAME:PASSWORD@localhost``

Dabei sind die Parameter ``SMTPSERVER``, ``USERNAME`` und ``PASSWORD`` entsprechend anzupassen.

OAUTH2_ENCRYPTION_KEY
#####################

Ein Verschlüsselungsschlüssel für die OAuth2 Authentifizierung. Dieser kann mittels

.. code-block:: shell

    $ php -r 'echo base64_encode(random_bytes(32)), PHP_EOL;'

erzeugt werden und anschließend eingefügt werden.
