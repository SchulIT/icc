---
sidebar_position: 3
---

# Datenkonsistenz

Da das System in Nordrhein-Westfalen entwickelt wurde, ist es insbesondere auf die hierzulande verwendete Software für
Schulverwaltung (SchILD) sowie die sehr häufig eingesetzte Planungssoftware Untis ausgerichtet.

## SchILD NRW

Aus der Schulverwaltungssoftware SchILD können mithilfe des [SchILD ICC Importer](https://schulit.de/software/schild-icc-importer)
alle Stammdaten importiert werden.

## Untis

Aus Untis stammen alle Planungsdaten wie Stundenplan, Aufsichten, Vertretungsplan, Klausurplan, Raumliste. Damit diese
Daten auf dem ICC ordnungsgemäß verarbeitet werden können, gelten folgende Regeln.

:::danger Achtung
Ein nicht-einhalten dieser Regeln führt dazu, dass das ICC nicht ordnungsgemäß arbeitet.
:::

* Die Lernenden und Lehrkräfte (sowie deren Mitgliedschaft in Lerngruppen) werden stets aus SchILD NRW importiert.
* Da Untis keine Kurse kennt (sondern nur Fächer), müssen Kurse entsprechend als Fach in Untis angegeben werden (natürlich mit der korrekten Klasse).

:::danger Wichtig
Die folgende Regel muss immer gründlich überprüft werden.
:::

* Fächer in Untis müssen eindeutig in SchILD zu erkennen sein. Das bedeutet konkret: Die Kombination aus Kursname, Klasse(n) und Lehrkraft **muss eindeutig** sein.

:::tip Tipp
Damit die obige Regel eingehalten wird, hat sich folgendes Schema zur Benennung der Kurse bzw. Fächer bewährt:

* Da Kursbezeichnungen in der Oberstufe in der Regel eindeutig sind, können diese einfach als Fach geführt werden (z.B. M-GK1, M-GK2 usw.).
* Für Kurse in der Sekundarstufe I ist es ratsam, einen Suffix für die Klasse(n) anzuhängen:
  * IF-a ist der Informatikkurs in der A, IF-b in der B usw.
  * IF-ab ist der Informatikkurs für A und B
  * IF-x ist die Abkürzung für A, B, C usw. (also für die gesamte Jahrgangsstufe)
  * IF-x1, IF-x2, ... benennt man die Kurse, wenn die Kurszusammensetzung aus Lernenden der gesamten Jgst. besteht und es mehrere dieser Kurse gibt

Wenn im Stundenplan Fächer in unterschiedlichen Zusammensetzungen unterrichtet werden (z.B. weil Lernzeiten mit AGs gekoppelt sind), müssen
diese Fächer entsprechend in Kurse separiert werden (insb. wenn sie von derselben Lehrkraft unterrichtet werden), also z.B. `LEZ-1-a` (Lernzeit
der A am Montag), `LEZ-3-a` (Lernzeit der A am Mittwoch) usw. 
:::