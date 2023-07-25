---
sidebar_position: 1
---

# SchILD NRW

Der Import aus SchILD NRW erfolgt mithilfe des [SchILD ICC Importers](https://schulit.de/software/schild-icc-importer).

## Installation

Die Installation erfolgt über das bereitgestellte MSI-Paket auf einem Computer (oder Server) in der Schulverwaltung. Der
Computer muss Zugriff auf die Datenbank von SchILD haben.

:::warning Wichtig
Aktuell (Stand Sommer 2023) wird eine Microsoft SQL-Datenbank vorausgesetzt. Für das neue SchILD 3.0 wird es einen
neuen Importer geben, der dann (das einzig verfügbare DMBS) MariaDB-Datenbanken unterstützt.
:::

## Konfiguration

Die Konfiguration erfolgt über die *Datei*-Schaltfläche nach dem Start des Tools. 

### SchILD-Einstellungen

| Einstellung                         | Wert                                                                |
|-------------------------------------|---------------------------------------------------------------------|
| Verbindungszeichenfolge             | Server=SERVER\SQLEXPRESS;Database=DATABASE;Integrated_Security=True |
| Nur sichtbare Elemente exportieren  | ✔️ Häckchen setzen                                                  |
| Lernende mit Status berücksichtigen | Aktive, Beurlaubt, Abitur, Abgaenger                                |

Bei der Verbindungszeichenfolge müssen `SERVER` und `DATABASE` entsprechend ausgetauscht werden. 

:::danger Wichtig
Der ausführende Windows-Benutzer muss Leserechte auf der Datenbank haben (siehe Parameter `Integrated_Security=true`).
:::

:::tip Gewusst
Es ist auch möglich, den Benutzernamen und das zugehörige Passwort für einen SQL-Benutzer zu hinterlegen. Dazu den
Parameter `Integrated_Security=True` entfernen und stattdessen `User=USERNAME;Password=PASSWORD` verwenden (`USERNAME` und
`PASSWORD` natürlich entsprechend ersetzen).
:::

### ICC Einstellungen

| Einstellung | Wert                                                    |
|-------------|---------------------------------------------------------|
| URL         | https://icc.schulit.de                                  |
| Token       | Das in der `.env.local` festgelegte `IMPORT_PSK` Token. |

Natürlich die URL entsprechend anpassen.

:::tip Tipp
Es ist ratsam, nach dem Speichern der Einstellungen das Programm einmal zu schließen und wieder zu öffnen.
:::

## Importvorgang

Die gewünschten Optionen sowie den entsprechenden Abschnitt auswählen und auf "Import starten" klicken.

## Fehlerbehebung

### Wechsel Klassen- und Kursunterricht

Wenn ein Klassenunterricht zu einem Kursunterricht umgewandelt wird (oder umgekehrt), kommt es zu
Problemen beim Import, weil das Tool diese Änderung nicht sieht und stattdessen den alten Unterricht
löscht und einen neuen anlegen möchte. Das wird jedoch verhindert, sobald man Unterrichtsbücher verwendet.

Damit das System den neuen Unterricht wiedererkennt, muss die externe ID händisch angepasst werden.

#### Fall 1: Aus Klassen- wird Kursunterricht

Zunächst muss man in der SchILD-Datenbank die Kurs ID herausfinden. Es handelt sich dabei um den Wert
der Spalte `ID` des zugehörigen Datensatzes in der Tabelle `Kurse`.

Diese muss nun im ICC unter *Verwaltung ➜ Datenverwaltung ➜ EasyAdmin ➜ Unterrichte* oben den Kurs suchen und über
das Drei-Punkte-Menü den entsprechenden Kurs ändern. Dort muss die ID aus der Datenbank als *Externe ID* eingetragen werden.

Anschließend funktioniert der Import wieder.

#### Fall 2: Aus Kurs- wird Klassenunterricht

Die externe ID für Klassenunterrichte wird gebildet aus dem Fachkürzel und dem Namen der Lerngruppe, diese ist
bei Klassenunterrichten stets gleich dem Namen der Klasse. Bei mehreren Klassen werden diese mit einem Bindestrich
kombiniert. Beispiele:

- Mathe, 6c: M-06C
- Informatik, 6c und 6d: IF-06C-06D