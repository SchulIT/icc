Installation
============

Schritt 1: Anwendung installieren
---------------------------------

Möglichkeit 1: Installation mit Git
###################################

Zunächst mittels Git den Quelltext des Projektes auschecken:

.. code-block:: shell

    $ git clone https://github.com/schulit/icc.git
    $ git checkout -b 1.0.0

Dabei muss ``1.0.0`` durch die gewünschte Version ersetzt werden.

Anschließend in das Verzeichnis ``icc`` wechseln und alle Abhängigkeiten installieren:

.. code-block:: shell

    $ composer install --no-dev --optimize-autoloader --no-scripts

Die Direktive ``no-scripts`` ist wichtig, da es ansonsten zu Fehlermeldungen kommt.

.. warning:: Der folgende Teil funktioniert nur, wenn Node und yarn verfügbar sind. Falls die beiden Tools nicht verfügbar sind, müssen die Dateien manuell hochgeladen werden.

Nun müssen noch die Assets installiert werden:

.. code-block:: shell

    $ yarn encore production

Möglichkeit 2: Installation ohne Git
####################################

Den Quelltext der Anwendung von `GitHub <https://github.com/schulit/icc/releases>`_ herunterladen und auf dem Webspace
entpacken. Anschließend kann mit der Konfiguration fortgefahren werden.

Schritt 2: Konfiguration
------------------------

Nachdem der Quelltext und seine Abhängigkeiten installiert sind, müssen alle benötigten Zertifikate und Konfigurationsdateien erstellt werden.

Schritt 2.1: Konfigurationsdatei erstellen
##########################################

Siehe `Konfiguration <configuration.html>`_.

Schritt 2.2: Zertifikate erstellen
##################################

Damit das ICC mit dem Identity Provider sprechen kann, wird ein entsprechendes Zertifikat benötigt. Das Zertifikat kann über die Konsole
erstellt werden:

.. code-block:: shell

    $ php bin/console app:create-certificate --type saml

Anschließend werden einige Daten abgefragt. Diese können abgesehen vom ``commonName`` frei beantwortet werden.

- ``countryName``, ``stateOrProvinceName``, ``localityName`` geben den Standort der Schule an
- ``organizationName`` entspricht dem Namen der Schule
- ``organizationalUnitName`` entspricht der Fachabteilung der Schule, welche das ICC administriert, bspw. Schulname und IT-Suffix
- ``commonName`` Domainname des ICCs, bspw. ``icc.example.com``
- ``emailAddress`` entspricht der E-Mail-Adresse des Administrators

Anschließend muss noch ein Zertifikat für die OAuth2-Authentifizierung erstellt werden, damit sich Apps mit dem ICC verbinden können:

.. code-block:: shell

    $ php bin/console app:create-certificate --type oauth2

Es werden erneut einige Daten abgefragt (siehe oben).

Schritt 2.3: Identity Provider bekannt machen
#############################################

Im Identity Provider unter Verwaltung > IdP Details öffnen. Den Inhalt der angezeigten XML-Datei in die Zwischenablage kopieren
und anschließend die Datei ``saml/idp.xml`` einfügen (die Datei muss erstellt werden).

Der Inhalt der XML-Datei sieht dann folgendermaßen aus (Einträge mit ... sind pro System individuell):

.. code-block:: xml

    <?xml version="1.0"?>
    <EntityDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" entityID="...">
      <IDPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
        <KeyDescriptor use="encryption">
          <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
            <ds:X509Data>
              <ds:X509Certificate>..</ds:X509Certificate>
            </ds:X509Data>
          </ds:KeyInfo>
        </KeyDescriptor>
        <KeyDescriptor use="signing">
          <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
            <ds:X509Data>
              <ds:X509Certificate..</ds:X509Certificate>
            </ds:X509Data>
          </ds:KeyInfo>
        </KeyDescriptor>
        <SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="..."/>
        <SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="..."/>
      </IDPSSODescriptor>
    </EntityDescriptor>

Schritt 3: Installation abschließen
-----------------------------------

Nun folgende Kommandos ausführen, um die Installation abzuschließen:

.. code-block:: shell

    $ php bin/console cache:clear
    $ php bin/console doctrine:migrations:migrate --no-interaction
    $ php bin/console app:setup
    $ php bin/console shapecode:cron:scan

Schritt 4: ICC im Identity Provider registrieren
------------------------------------------------

Schritt 4.1: Dienst erstellen
#############################

Im Identity Provider muss das ICC als neuer Dienst registriert werden.

- Entity ID: Ist die in der Konfigurationsdatei gewählte Entity ID.
- Name: ICC*
- Beschreibung: *
- Assertion Customer Service URL: ``https://icc.example.com/saml/login_check`` (dabei muss ``icc.example.com`` durch die korrekte URL zum ICC ersetzt werden)
- URL: ``https://icc.example.com`` (dabei muss ``icc.example.com`` durch die korrekte URL zum ICC ersetzt werden)
- Zertifikat: hier muss der Inhalt der Datei ``saml/sp.crt`` hineinkopiert werden

Schritt 4.2: Attribut für Rollen erstellen
##########################################

Im nächsten Schritt muss ein Attribut erstellt werden, welches die ICC Rolle eines Benutzers speichert und dem ICC übermittelt. Dazu
im Identity Provider unter Verwaltung > Attribute ein neues Attrribut anlegen.

- Name: ``icc-roles``
- Anzeigename: ICC Rollen
- Beschreibung: Rollen, die der Benutzer im ICC annimmt
- Benutzer können dieses Attribut ändern: nicht aktiviert
- SAML Attribut-Name: ``urn:roles``
- Typ: Auswahlfeld
- Dienste: ICC

Anschließend unten unter Optionen folgende Optionen konfigurieren:

- Mehrfach-Auswahl möglich: aktiviert
- Optionen: hier die einzelnen Benutzerrollen eintragen (siehe `Benutzerrollen <roles.html>`_). Der Schlüssel ist der
  Name der Rolle (Präfix ``ROLE_``) und der Wert ist der Anzeigename.

Schritt 5: ICC im Webspace einrichten
-------------------------------------

Das ICC muss auf einer Subdomain (bspw. ``icc.example.com``) betrieben werden. Das Betreiben des ICC in einem Unterordner
wird nicht unterstützt.

.. warning:: Der Root-Pfad der Subdomain muss auf das ``public/``-Verzeichnis zeigen. Anderenfalls funktioniert das ICC nicht und es können wichtige Konfigurationsdaten abgerufen werden.