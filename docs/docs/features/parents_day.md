# Elternsprechtag

Mithilfe des Elternsprechtags-Feature können Elternsprechtage geplant werden. Die grundlegenden Funktionen sind:

* Bereitstellen von Terminen durch Lehrkräfte
  * flexible Zeitpläne sind möglich
  * Termine mit mehreren Kindern bzw. mehreren Lehrkräften möglich
* markieren von "Termin nicht notwendig" bzw. "Termin erforderlich" als Rückmeldung für Eltern
* Blockieren von Terminen
* Buchen von Terminen durch Eltern oder volljährige Lernende
* Absage von allen Terminen im Falle von Krankheit durch Lehrkraft oder Eltern
* Kollisionserkennung und -verhinderung
* Integration in Übersichtsseite
* Integration ins Benachrichtigungssystem

## Elternsprechtage erstellen

Über *Verwaltung ➜ Datenverwaltung ➜ Elternsprechtag* kann der Administrator (Benutzer mit der Rolle `ROLE_ADMIN`) 
Elternsprechtage erstellen. Dabei können folgende Eigenschaften angegeben werden:

| Eigenschaft         | Beschreibung                                                           |
|---------------------|------------------------------------------------------------------------|
| Titel               | Ein Name für den Elternsprechtag (bspw. *Elternsprechtag 2. HJ*)       |
| Datum               | Das Datum des Elternsprechtags                                         |
| Start Terminbuchung | Das Datum, ab dem das Buchen von Terminen möglich ist.                 |
| Ende Terminbuchung  | Das Datum, bis zu dem das Buchen von Terminen möglich ist (inklusive). |

:::warning Hinweis
Das letztmögliche Datum für das Ende von Terminbuchungen ist am Tag vor dem Elternsprechtag. Das System stellt dies sicher.
:::

:::tip Tipp
Es ist ratsam, dass Lehrkräften ein Zeitraum eingeräumt wird, um buchbare Termine zu erstellen.
:::

## Bereitstellung von Terminen

Bevor Eltern Termine buchen können, muss die Lehrkraft Termine erstellen. Diese können dann ab dem festgelegten Datum (siehe
oben) von Eltern gebucht werden.

### Bereitstellung mehrerer Termine

Am einfachsten ist es, wenn die Lehrkraft mehrere Termine anhand eines Plans anlegt. Dabei gibt man Beginn und Ende an sowie
die Dauer pro Termin. Anschließend legt das System entsprechende Termine an. 

Für den Fall, dass im genannten Bereich bereits Termine existieren, kann die Lehrkraft folgende Strategien auswählen:
* alte Termine löschen (sofern nicht gebucht)
* alte Termine beibehalten und nur anlegen, sofern Zeitbereich frei ist

Einschränkungen:
* es können keine festgelegten Pausen zwischen den Terminen erstellt werden

### Bereitstellung eines einzelnen Termins

Es ist auch möglich, einzelne Termine außerhalb eines festgelegten Rasters anzulegen. Es kann eine beliebige Uhrzeit
und Dauer angegeben werden.

## Blocken von Terminen

Es ist möglich, einzelne Termine zu blockieren, sodass sie nicht gebucht werden können.

## Buchen von Terminen

### Eltern

Eltern sehen auf der Übersichtsseite zum Elternsprechtag alle Lehrkräfte, die ihre Kinder unterrichten. Darin ist auch zu
sehen, ob die Lehrkräfte einen Termin wünschen oder kein Termin notwendig ist.

Das Buchen der Termine erfolgt nach dem "first come first serve"-Prinzip. Sobald ein Termin gebucht ist, kann er von keinem
weiteren Elternteil gebucht werden. Termine können nachträglich umgebucht werden, indem ein neuer Termin gebucht wird.
Der alte Termin wird anschließend wieder frei und kann von anderen Eltern gebucht werden.

:::tip Gewusst
Das Buchen mehrerer Termine bei einer Lehrkraft durch Eltern ist nicht möglich.
:::

### Lehrkräfte

Lehrkräfte können auch Termine buchen, indem sie Lernende den entsprechenden Terminen hinzufügen.

## Terminabsage

Lehrkräfte können einzelne oder alle Termine (bspw. aufgrund von Krankheit) absagen. Eltern werden entsprechend informiert
und die Übersichtsseite zeigt die Absage an.

Für den Fall, dass man als Elternteil einen Termin kurzfristig nicht wahrnehmen kann (bspw. durch Krankheit), können
diese ebenfalls abgesagt werden. Diese Absage ist erst möglich, wenn das Buchungszeitfenster für den Elternsprechtag 
geschlossen ist.

## Kollisionserkennung

Das System prüft beim Buchen von Terminen, ob eine Kollision vorliegt. Es wird geprüft, ob alle beteiligten Lehrkräfte
und alle beteiligten Lernenden zum Zeitpunkt des Termins keinen anderen Termin haben. So werden doppelt belegte Termine
vermieden.

## Integration Übersichtsseite

Am Tag des Elternsprechtages sieht man die persönliche Übersicht auf der Startseite des ICC. Bereits vorab werden Elternteile
informiert, sobald die Buchung von Terminen möglich ist.

## Integration ins Benachrichtigungssystem

Beim Buchen, Umbuchen oder Absagen von Terminen werden automatisch alle beteiligten Parteien (Eltern, volljährige Kinder sowie
Lehrkräfte) informiert.

