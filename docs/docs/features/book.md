
# Unterrichtsbuch

Mithilfe von Unterrichtsbüchern kann der Unterricht digital dokumentiert werden. Dabei wird ausgenutzt, dass das ICC
alle Unterrichte eines Schuljahresabschnittes kennt. Die einzelnen Unterrichtsbücher lassen sich dann zu Klassenbüchern
zusammenführen.

## Limitierungen

### Einträge für zukünftige Tage

Da sich der Stundenplan für zukünftige Tage durch einen Import ändern kann, muss beim Vorabeintragen darauf beachtet werden,
dass diese durch einen erneuten Stundenplanimport gelöscht werden können.

:::tip Gewusst
Es ist Bad-Practise Einträge vorab zu erstellen.
:::

### Beliebige Einträge

Einträge können nur für die im Stundenplan hinterlegten Stunden erstellt werden. Freie Einträge (z.B. bei Sondereinsätzen) 
können nicht angelegt werden. 

:::info GitHub
Hierzu existiert bereits ein Issue im Hinblick auf [Sondereinsätze](https://github.com/SchulIT/icc/issues/224)
:::

:::success Workaround
Als Workaround müssen die Sondereinsätze händisch in der Stunde eingetragen werden, in der sie stattfinden. Dabei
muss dann das Vertretungsfach entsprechend geändert werden.
:::

### Ein Eintrag pro Unterrichtsstunde

Es ist nicht möglich, denselben Eintrag für verschiedene Unterrichtsstunden zu erstellen. Beispiel:

1. Stunde: Deutsch
2. Stunde: Deutsch
3. Stunde: Mathematik
4. Stunde: Mathematik

✔ Einträge für jeweils 1./2. und 3./4. Stunde erstellen (= 2 Einträge)  
✔ Einträge für jeweils 1., 2. und 3./4. Stunde erstellen (= 3 Einträge)  
✔ Einträge für jeweils 1./2. und 3., 4. Stunde erstellen (= 3 Einträge)  
✔ Einträge für jeweils 1., 2., 3. und 4. Stunde erstellen (= 4 Einträge)  
❌ Eintrag für 1.-4. Stunde erstellen (= 1 Eintrag)  

:::tip Gewusst
Das Eintragen von Unterrichtsstunden über mehrere Stunden (bspw. bei Doppelstunden) ist nur möglich, wenn diese bereits
als solche (also Doppelstunden) importiert werden.
:::

## Unterricht dokumentieren

Unterricht kann entweder stattfinden (in welcher Form auch immer) oder ausfallen.

### Entfall

Entfälle müssen entsprechend als Enfälle markiert werden. Bei Entfällen erfolgt keine Anwesenheitskontrolle. Dazu wählt
man entsprechend die Funktion *Einzelstunde entfällt* oder *Doppelstunde entfällt*.

### Eintrag für Stunde

### Anwesenheitsliste

Über die Anwesenheitsliste kann die Anwesenheit festgehalten werden. Oberhalb werden dabei Abwesenheitsvorschläge angezeigt.
Dabei handelt es sich jedoch ausschließlich um Vorschläge, die Lehrkraft darf diese nicht blind übernehmen. Die Vorschläge
werden wie folgt erstellt:

* Absenz aufgrund einer Abwesenheitsmeldung
* Absenz in einer vorherigen Stunde (und es liegt keine Abwesenheitsmeldung vor)
* Klausur (es kann durchaus sein, dass der Schüler/die Schülerin an einer Klausur des eigenen Unterrichts teilnimmt)
* Entschuldigung liegt vor

#### Anwesenheitsstati

Es wird zwischen

* anwesend
* verspätet mit Zeitangabe (=anwesend mit Verspätung)
* nicht anwesend

unterschieden. Bei einer Nicht-Anwesenheit kann die Anzahl der Fehlstunden und der Entschuldigungsstatus festgelegt werden.
Diese werden bei der Übernahme aus den Abwesenheitsvorschlägen ggf. übertragen (z.B. wenn aufgrund des Grundes der Absenz
keine Fehlstunden gezählt werden sollen).

Im Kommentarfeld lassen sich Kommentare hinterlegen, die die An- oder Abwesenheit betreffen.

:::danger Wichtig
Hier sollen keine Kommentare zum Verhalten (o.ä.) des Kindes eingetragen werden.
:::

### Aufgaben

Unter Aufgaben lassen sich Hausaufgaben notieren. Diese werden auch für die Lernenden auf der [Übersichtsseite](./dashboard)
angezeigt.

### Bemerkungen zur Lerngruppe

Hier lassen sich Bemerkungen zur Lerngruppe hinterlegen. Dieses Feld sollte nur ausgefüllt werden, wenn die Bemerkung die
gesamte Lerngruppe betrifft.

:::tip Gewusst
Das Eintragen einer Bemerkung erzeugt keine Benachrichtigung (z.B. bei der Klassenleitung).
:::

## Entschuldigungen

Je nach Entschuldigungsregelung lassen sich Entschuldigungen auf zwei Arten eintragen:

1. Pro Unterrichtsstunde, indem man den Eintrag öffnet oder die Lernendenansicht des betroffenen Kindes öffnet
2. Einen Gesamtzeitraum über das Menü *Entschuldigungen* eintragen

Für Klassen bzw. Jahrgangsstufen, in denen die Fachlehrkraft entschuldigt, wird die erste Möglichkeit genutzt. Für Klassen
bzw. Jahrgangsstufen, in denen die Klassenleitung entschuldigt, wird Möglichkeit 2 genutzt.

:::tip Gewusst
Fehlende Entschuldigungen werden im Kopfbereich des Unterrichtsbuches angezeigt.
:::

:::warning Achtung
Bei Möglichkeit 1 können nur Unterrichtsstunden entschuldigt werden, für die bereits ein Eintrag vorliegt. Verspätet sich
die Eintragung einer Unterrichtsstunde, so können bereits vermeintlich entschuldigte Stunden wieder auftauchen.
:::

## Kommentare

Über Kommentare werden klassische Klassenbucheinträge realisiert. Wird ein Kommentar verfasst, so wird die Klassenleitung
automatisch informiert.

:::tip Gewusst
Eltern werden aktuell (Stand Sommer 2023) noch nicht informiert. Siehe [GitHub-Issue](https://github.com/SchulIT/icc/issues/330).
:::

## Deaktivierung von Unterrichtsbüchern

Standardmäßig sind Unterrichtsbücher für alle Unterrichte aktiviert. Die Deaktivierung kann über *Verwaltung ➜ Datenverwaltung
➜ EasyAdmin ➜ Unterrichte* vorgenommen werden.