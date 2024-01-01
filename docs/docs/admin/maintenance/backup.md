---
sidebar_position: 20
---

# Datensicherung

Die Anwendung besitzt von sich aus kein Backup-Skript zur Verfügung. Das Backup muss daher händisch erfolgen.

## Datenbank

Zur Sicherung der Datenbank kann (zum Beispiel) das Tool `mysqlbackup` verwendet werden.

### Datenbank sichern

Mit dem folgenden Kommando kann die Datenbank gesichert werden:

```bash
$ mysqlbackup -u USER -p DATENBANK > icc.sql
```

`USER` und `DATENBANK` müssen entsprechend durch den Datenbanknutzer und den Namen der Datenbank ersetzt werden. Der
Parameter `-p` bewirkt, dass das Passwort eingegeben werden muss.

### Datenbank zurückspielen

Mit dem folgenden Kommando kann die Datenbank zurückgespielt werden:

```bash
$ mysql -u USER -p DATENBANK < icc.sql
```

`USER` und `DATENBANK` müssen entsprechend durch den Datenbanknutzer und den Namen der Datenbank ersetzt werden. Der
Parameter `-p` bewirkt, dass das Passwort eingegeben werden muss.

## Wichtige Dateien

Folgende Dateien müssen im Backup enthalten sein:

* `.env.local`
* `saml/idp.xml`
* `saml/sp.crt`
* `saml/sp.key`
* `files/*`
* optional: `assets/css/custom/*.scss`
* optional: `assets/css/custom/*.scss`
* optional: `public/images/*`

## Backup über Konsole

Mit Version 2.3 ist es möglich, das Datenbankbackup über die Konsole automatisiert zu erstellen und bei Bedarf zurückzuspielen.
Das Backup erzeugt eine ZIP-Datei im Ordner `backup/` und muss mit höchster Sicherheit behandelt werden, da es alle wichtigen
Daten des ICC enthält.

Folgende Dateien bzw. Ordner werden gesichert:

* `.env.local`
* `saml/*` (Ordner)
* `files/*` (Ordner)

Zusätzlich wird in der Datei `dump.sql` der SQL-Dump der Datenbank erstellt.

:::caution Hinweis
Das Kommando sichert die optionalen Dateien (siehe oben) **nicht**.
:::

:::danger Achtung
Das Kommando sollte nicht für automatisierte Backups genutzt werden. Parallele Backups können (müssen jedoch nicht) Probleme
erzeugen, was die Anwendung nicht überprüft. Hinzu kommt, dass Backups groß werden können (je nach Anzahl und Größe der
hochgeladenen Dateien).
:::

### Voraussetzungen

Die MySQL-Binaries `mysql` und `mysqldump` müssen auf dem System verfügbar und für PHP abrufbar sein. Anderenfalls scheitert
das Backup.

### Backup erstellen

```bash
$ php bin/console app:backup:create
```

### Backup wiederherstellen

Beim Einspielen des Backups muss die Datei `.env.local` bereits vorhanden sein. Wird das Backup auf ein neues System gemacht,
so muss die Datei händisch extrahiert werden, da ansonsten der Konsolenaufruf scheitert.

```bash
$ php bin/console app:backup:restore
```

Das obige Kommando listet zunächst alle ZIP-Dateien im `backup/`-Ordner auf und fragt, welches Backup eingespielt werden soll.
Erst nach einer Bestätigung wird das Backup eingespielt.

:::danger Achtung
Das Backup **ersetzt** alle Dateien mit den Dateien aus dem Backup. Dateien, die nicht Teil des Backups sind, werden dabei
ebenfalls gelöscht. Es wird empfohlen, vor dem Einspielen des Backups ein neues Backup zu erstellen.
:::

### Backup auf anderem Server einspielen

Das Einspielen des Backups auf einen anderen Server wird grundsätzlich unterstützt. Es muss jedoch händisch die Datei `.env.local`
aus dem Backup extrahiert und in das Projektverzeichnis gelegt werden sowie die entsprechende Backup-ZIP in das `backup/`-Verzeichnis
kopiert werden.