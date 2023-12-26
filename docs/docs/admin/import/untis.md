---
sidebar_position: 2
---

# Untis

Der Import aus Untis erfolgt mithilfe der von Untis bereitgestellten GPU-Dateien und HTML-Dateien. Die dazu
benötigten HTML-Exportformate werden durch das ICC bereitgestellt und müssen zunächst importiert werden.

## Benötigte Untis-Erweiterung
Es wird eine Untis-Grundlizenz vorausgesetzt. Eine MultiUser-Lizenz ist nicht erforderlich, die Nutzung von Untis
MultiUser sollte jedoch kein Problem darstellen.

Um den HTML-Vertretungsplan zu importieren, wird das Info-Stundenplanmodul benötigt. Zwar lässt sich der Vertretungsplan
auch über die GPU014 importieren, allerdings dann ohne Absenzen und Tagestexte. Der Import der GPU014 wird jedoch nicht
empfohlen, da Untis selbst das Format nicht mehr unterstützt und sich darin teilweise merkwürdige Einträge befinden.

## Einschränkungen

Der Stundenplanimport funktioniert aktuell nur für periodische Stundenpläne (bspw. A-/B-Wochen). Nicht-periodische
Stundenpläne werden nicht unterstützt, eine Unterstützung kann jedoch bei Bedarf eigenständig programmiert werden 😉

## Vorbereitung

### Formate importieren (Untis)

**Download:** Die Formate befinden sich auf `GitHub <https://github.com/SchulIT/icc/tree/master/public/static>` und können von dort heruntergeladen werden.

Zunächst im Menü unter "Import/Export" die Kategorie "Untis" auswählen und dort die Schnittstelle "Formate/Fenstergruppen/Ribbon" anklicken.

![](/img/docs/untis-import-1.png)

Es öffnet sich ein Dialog. Dort in den Tab "Eingabeformat Import" (Vertretungsplan) oder "Stundenplanformat Import" (Stundenplan) wechseln und auf "Durchsuchen" klicken.

![](/img/docs/untis-import-2.png)
![](/img/docs/untis-import-2-stundenplan.png)

Es öffnet sich der "Datei öffnen"-Dialog. Dort in den Ordner wechseln, in dem die Formate-Datei liegt und anschließend die Datei auswählen und auf "Öffnen" klicken.

:::tip Wichtig
Um die Datei sehen zu können, muss unten rechts das "Format Dateien (*.gpf)" ausgewählt werden.
:::

![](/img/docs/untis-import-3.png)

Abschließend auf "Importieren" klicken. Es öffnet sich nun ein letzter Dialog, welchen man mit "Ok" bestätigen kann.

![](/img/docs/untis-import-4.png)
![](/img/docs/untis-import-4-stundenplan.png)

:::success Fertig
Dieses Prozedere sowohl für das Vertretungsplan- als auch das Stundenplanformat durchführen.
:::

### A-/B-Wochen angeben (ICC)

Als Erstes müssen die Stundenplanwochen im ICC festgelegt werden. Dazu unter *Verwaltung ➜ Datenverwaltung ➜ Stundenplan* jeweils eine
A- und B-Woche anlegen und die entsprechenden Wochen zuweisen.

### Übersetzungstabelle für Schulwochen (ICC)

Anschließend müssen noch alle Einstellungen für den Import festgelegt werden. Dazu wechselt man zu *Import ➜ Einstellungen*.

Während die meisten Optionen hier optional sind, muss die Übersetzungstabelle von Schulwochen zu Kalenderwochen erstellt werden.
Hat man das Info-Stundenplanmodul, so lässt sich hier die `date.txt` importieren, welche eine solche Übersetzungstabelle
darstellt.

Um diese `date.txt` zu erstellen, muss der Info-Stundenplan-Dialog in Untis geöffnet werden. In der Registerkarte "Datenbank"
wählt man als Startdatum den Schulbeginn aus und gibt dann 53 Wochen als "Maximale Wochenzahl" an. Nach dem Klicken auf "Exportieren"
landet die Datei `date.txt` im ausgewählten Exportverzeichnis.

![](/img/docs/untis-dates.png)

:::warning Hinweis
Die Übersetzungstabelle muss zu Beginn des Schuljahres neu importiert werden.
:::

### Optional: Fächer-Umbenennungen (ICC)

Manchmal werden Fächer in Untis und im Schulverwaltungsprogramm anders benannt. Daher lässt sich unter *Import ➜ Einstellungen*
angeben, wenn Fächer vor dem Import umbenannt werden sollen. Das ist wichtig, da anderenfalls die Stundenplanzuordnung zu
den Unterrichten nicht funktioniert.

## Import

### Vertretungsplan (GPU014)

Der Vertretungsplan kann über *Import ➜ Vertretungsplan (GPU014)* mithilfe der GPU014.txt importiert werden. Dazu muss
lediglich die GPU014 aus Untis exportiert und in die ICC-Maske eingefügt werden.

:::warning Wichtig
Absenzen, Unterrichtsfrei, Tagestexte oder Pausenaufsichtsvertretungen werden so nicht oder nicht korrekt importiert.
:::

### Vertretungsplan (HTML)

Die Erstellung des HTML-Vertretungsplans geschieht mithilfe des Info-Stundenplanmoduls. Vorher muss sichergestellt werden,
dass der Export korrekt konfiguriert ist. Im Info-Stundenplan-Dialog unter Monitor HTML muss die entsprechende Einstellung
richtig gesetzt sein:

* Anzahl Tage: Hier kann selbstständig festgelegt werden, für wie viele Tage der Vertretungsplan exportiert werden soll.
* Max. Zeilen pro Seite: Dieser Wert sollte möglichst hoch sein (9999 ist das Maximum), damit auch alle Vertretungen auf 
  eine Seite exportiert werden. Das ist wichtig, da das ICC sonst den Plan nicht korrekt importieren kann.
* Texte zum Tag auf jeder Seite: nur wenn diese Option aktiv ist, werden Tagestexte, Absenzen und unterrichtsfreie Zeiten exportiert
* Exportverzeichnis: darf frei gewählt werden

![](/img/docs/untis-export-2.png)

Zum Import können die exportierten HTML-Dateien einfach unter *Import ➜ Vertretungsplan (HTML)* hochgeladen werden.

### Klausuren

Klausuren werden über die GPU017 hochgeladen. Dabei muss auch die GPU002 (Unterrichte) angegeben werden, damit das ICC die
Klasse bzw. Jahrgangsstufe auflösen kann, die zu der Klausur gehört (in der GPU017 steht leider nur der Kursname und nicht
die Klasse/Jgst.; diese wird über die Unterrichtsnummer herausgefunden).

:::warning Wichtiges zur GPU002
Beim Export der GPU002 wird immer nur die aktuell ausgewählte Periode berücksichtigt.
:::

Da der Klausurimport fehleranfällig ist, bitte folgende Handlungsanweisungen beachten:

1. Immer eine zum Importzeitraum passende GPU002 hochladen (obigen Hinweis beachten!)
2. Der Importzeitraum darf keine im ICC definierten Schuljahresabschnitte (i.d.R. sind damit Halbjahre gemeint) überschreiten.
3. Wenn sich innerhalb eines Schuljahresabschnittes die Daten in Untis zu sehr ändern (und sich somit die GPU002 ändert!), 
muss der Klausurimport in mehreren Teilen erfolgen (pro Periode ein Import). Dazu (a) in Untis jeweils die Periode auswählen, GPU017 und GPU002 exportieren und (b) als Importzeitraum den Periodenzeitraum auswählen.

Der Importer berücksichtigt die Klausurschreibenden (falls nicht anders unter *Import ➜ Einstellungen* angegeben). Die Schülerinnen
und Schüler müssen daher in Untis angelegt sein. Nutzt man Kurs42, so können die Schülerinnen und Schüler automatisiert
angelegt werden. Außerdem haben sie dann auch bereits das richtige Format: `Nachname_Vorname_YYYYMMDD` (wobei `YYYYMMDD`
dem Geburtstag entspricht).

:::tip Gewusst
Es ist möglich, dass die Lernenden entweder immer, nie oder nur manchmal aus Untis importiert werden. Diese Unterscheidung
funktioniert über den (in Untis angegebenen) Namen der Klausur. Unter *Import ➜ Einstellungen* kann dazu ein regulärer Ausdruck
hinterlegt werden.

Die Regeln für Klausurschreibende werden unter *Verwaltung ➜ Einstellungen ➜ Import* hinterlegt.
:::

#### Zuordnung der Klausurschreibenden zu Unterrichten

Importiert man die Klausurschreibenden aus Untis, so ist für das System leider die genaue Kurszuordnung nicht ersichtlich. 
Das System rät daher die Kurszuordnung der Klausurschreibenden folgendermaßen:

Prüfe für alle der Klausur zugewiesenen Kurse und Klausurschreibenden:

* wenn der Lernende in einem der zugewiesenen Kurse ist:
  * prüfe, ob es eine "Regel für Klausurschreibende" für die Klasse des Lernenden gibt. Falls ja, überprüfe Regel und setze Kurs für den Lernenden entsprechend
  * falls es für die Klasse des Lernenden keine Regel gibt, setze den Kurs

Für den Fall, die Prüfung für mehrere Kurse zutreffend ist, so wird kein Kurs gesetzt.

:::tip Gewusst
Das Problem sollte nur bei Nachschreibklausuren auftreten (anderenfalls hat man einen Fehler in den Plandaten und ein Lernender schreibt zwei Klausuren parallel).
Um das Problem zu lösen, kann man solche Klausuren in Untis splitten, sodass pro Klausur nur ein Kurs enthalten ist. 
Dann ist die Zuordnung eindeutig und problemlos möglich.
:::tip

### Aufsichten

Die Pausenaufsichten können über *Import ➜ Aufsichten* mithilfe der GPU009.txt importiert werden. Dazu muss
lediglich die GPU009 aus Untis exportiert und in die ICC-Maske eingefügt werden.

:::warning Wichtig
Der Export der GPU009 erfolgt nur für die in Untis ausgewählte Periode. Die Aufsichten müssen daher für jede
Periode separat importiert werden. Dabei sollte das Importzeitfenster der Periode entsprechen.
:::

### Stundenplan (HTML)

Die Erstellung der HTML-Stundenplandateien erfolgt im Stundenplan-Modus von Untis. Dazu zunächst einen Klassenplan öffnen
und anschließend sicherstellen, dass (a) als Zeitraum die Periode und (b) das Format `Ex-K-HTML` ausgewählt ist.

![](/img/docs/untis-export-1-timetable.png)

Nun den Drucken-Dialog öffnen (Strg+P) und alle Klassen auswählen, die exportiert werden sollen.

![](/img/docs/untis-export-2-timetable.png)

Nun auf "HTML-Ausgabe" klicken und den Zielordner auswählen, in dem die HTML-Dateien erstellt werden sollen.

Für den Fall, dass es Fächer ohne Klassen gibt (bspw. Bereitschaften), so muss dasselbe Prozedere mit einem Fächerplan
gemacht werden. Es muss ebenfalls als Zeitraum die Periode und als Format `Ex-F-HTML` ausgewählt werden. Im Drucken-Dialog
werden dann nur die Fächer ausgewählt, die exportiert werden sollen.

Anschließend müssen die Fächer- bzw. Klassenpläne noch zu jeweils einem ZIP-Archiv hinzugefügt werden, damit sie unter
*Import ➜ Stundenplan* hochgeladen werden können.

![](/img/docs/untis-export-3-timetable.png)

:::danger Wichtig
Das ICC kennt - anders als Untis - keine Perioden. Ein Stundenplan im ICC gilt immer für einen bestimmten
Zeitraum. Diese Zeiträume können dieselben wie die in Untis definierten Perioden sein, müssen das aber nicht sein. Stundenpläne
können nur für Zeiträume, die in der Zukunft liegen, importiert werden. Der Stundenplan wird in diesem Zeitraum zunächst
gelöscht und anschließend durch den neuen Stundenplan ersetzt. Dabei werden bereits getätigte Unterrichtsbucheinträge
gelöscht.
:::