---
sidebar_position: 4
---

# Anwendung im Single Sign-On registrieren

Damit sich Benutzer am ICC anmelden können, muss die Anwendung im Single Sign-On registriert werden. 

## SAML-Zertifikat erstellen

Es wird ein selbst-signiertes Zertifikat mittels OpenSSL erzeugt. Dazu das folgende Kommando ausführen:

```bash
$ php bin/console app:create-certificate --type saml
```

Anschließend werden einige Daten abgefragt. Diese können abgesehen vom `commonName` frei gewählt werden:

* `countryName`, `stateOrProvinceName`, `localityName` geben den Standort der Schule an
* `organizationName` entspricht dem Namen der Schule
* `organizationalUnitName` entspricht der Fachabteilung der Schule, welche für die Administration zuständig ist
* `commonName` Domainname des ICC, bspw. `icc.schulit.de`
* `emailAddress` entspricht der E-Mail-Adresse des Administrators

:::info
Das Zertifikat ist standardmäßig 10 Jahre gültig.
:::

## Dienst beim Single Sign-On registrieren

:::warning Hinweis
Der folgende Schritt muss im Single Sign-On erledigt werden und **nicht** im ICC.
:::

### Dienst registrieren

Unter *Verwaltung ➜ Dienste* einen neuen SAML-Dienst erstellen.

Einige Metadaten lassen sich automatisiert laden, indem man zunächst die Metadaten-XML `https://icc.schulit.de/saml/metadata.xml`
(`icc.schulit.de` durch die BookStack-Domain ersetzen) einträgt und auf *Herunterladen* klicken.

Anschließend müssen noch der Name und eine passende Beschreibung eingetragen werden.

### Attribut für Rolle erstellen

Mittels Rollen wird konfiguriert, was Benutzer im ICC dürfen und was nicht. Diese werden als Attribut im Single Sign-On
gespeichert und entsprechend beim Anmelden am ICC weitergeleitet.

Unter *Verwaltung ➜ Attribute* ein neues Attribut erstellen.

| Option                                 | Wert                          |
|----------------------------------------|-------------------------------|
| Name                                   | icc-roles                     |
| Anzeigename                            | ICC Rollen                    |
| Beschreibung                           | *beliebig*                    |
| Benutzer können dieses Attribut ändern | ❌ Häckchen nicht setzen       |
| SAML Attribut-Name                     | urn:roles                     |
| Typ                                    | Auswahlfeld                   |
| Dienste                                | Hier den ICC-Dienst auswählen |

Unter *Optionen* muss die Option *Mehrfach-Auswahl möglich* deaktiviert bleiben.

Folgende Optionen eintragen:

| Schlüssel                    | Wert                                             |
|------------------------------|--------------------------------------------------|
| ROLE_USER                    | Benutzer                                         |
| ROLE_ADMIN                   | Administrator                                    |
| ROLE_SUPER_ADMIN             | Super-Administrator                              |
| ROLE_MESSAGE_CREATOR         | Mitteilungen erstellen                           |
| ROLE_MESSAGE_PRIORITY        | Mitteilungen mit Priorität erstellen             |
| ROLE_MESSAGE_ADMIN           | Mitteilungs-Administrator                        |
| ROLE_DOCUMENTS_ADMIN         | Dokumente-Administrator                          |
| ROLE_APPOINTMENT_CREATOR     | Termine erstellen                                |
| ROLE_APPOINTMENTS_ADMIN      | Termine-Administrator                            |
| ROLE_WIKI_ADMIN              | Wiki-Administrator                               |
| ROLE_EXAMS_CREATOR           | Klausuren erstellen                              |
| ROLE_EXAMS_ADMIN             | Klausuren-Administrator                          |
| ROLE_KIOSK                   | Kiosk-Benutzer                                   |
| ROLE_STUDENT_ABSENCE_CREATOR | Krankmeldungen erstellen                         |
| ROLE_STUDENT_ABSENCE_VIEWER  | Krankmeldungen einsehen                          |
| ROLE_TEACHER_ABSENCE_MANAGER | Benutzer darf Absenzen von Lehrkräften verwalten |
| ROLE_BOOK_ENTRY_CREATOR      | Unterrichtsbücher (schreibend)                   |
| ROLE_BOOK_VIEWER             | Unterrichtsbücher (lesend)                       |
| ROLE_SHOW_BIRTHDAY           | Geburtstage anzeigen (Lernende)                  |


Eine genaue Erklärung zu den Rollen und wie diese zugewiesen werden sollten, gibt es im Abschnitt [Benutzerrollen](../roles).

### Single Sign-On beim ICC hinterlegen

Damit das ICC den Single Sign-On kennt, muss noch eine XML-Datei hinterlegt werden.

Unter *Verwaltung ➜ IdP Details* den XML-Teil in die Zwischenablage kopieren und den Inhalt in der Datei `saml/idp.xml`
im ICC-Ordner hinterlegen.