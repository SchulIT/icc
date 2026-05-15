# Information & Communication Center

![GitHub Workflow Status (with event)](https://img.shields.io/github/actions/workflow/status/schulit/icc/php.yml?style=flat-square)
![PHP 8.4](https://img.shields.io/badge/PHP-8.4-success.svg?style=flat-square)
![PHP 8.5](https://img.shields.io/badge/PHP-8.5-success.svg?style=flat-square)
![AGPL3.0 License](https://img.shields.io/github/license/schulit/icc.svg?style=flat-square)


Das ICC bietet die Webansicht von Stunden-, Vertretungs-, Klausur- und Terminplan sowie die Möglichkeit über Mitteilungen 
mit bestimmten Gruppen der Schulgemeinde kommunizieren.

## Funktionen

* Dashboard mit aktuellem Tagesplan (Stundenplan mit integriertem Vertretungsplan)
* Pläne (Stunden-, Vertretungs-, Klausur-, Termin- und Raumplan)
    * Export von Stunden-, Klausur- und Terminplan als ICS (auch als Abo für den Google Kalender oder Office 365)
* Übersicht der Lerngruppen und Unterrichte
* Planung von Klausuren (bspw. für die Sekundarstufe I)
* Raumreservierungen
* Mitteilungen (wie ein schwarzes Brett)
    * Anhänge
    * Bestätigungen einfordern
    * personalisierte Anhänge
    * personalisierte Dateirückmeldungen
    * Zielgruppenspezifisch (Lehrkräfte, Schülerinnen und Schüler bestimmter Lerngruppen, Eltern, Praktikanten)
* Dokumentablage zur Ablage von Informationen (bspw. Konzepte, Lehrpläne, schulinterne Regelungen)
    * Anhänge
    * zielgruppenspezifisch (Lehrkräfte, Schülerinnen und Schüler bestimmter Lerngruppen, Eltern, Praktikanten)
* Wiki-Funktion für Anleitungen
    * hierarchischer Aufbau möglich
    * zielgruppenspezifisch (Lehrkräfte, Schülerinnen und Schüler bestimmter Lerngruppen, Eltern, Praktikanten)

## Handbuch

Das Handbuch ist [hier](https://docs.schulit.de/icc) zu finden. 

## Über das ICC

Die Anwendung ist Teil der SchulIT Software Suite und kann kostenlos und auf eigene Gefahr installiert werden.

Das ICC ist eine Symfony 7.4 Anwendung.

## Wichtige Information

Aktuell kann das ICC genutzt werden, wenn als Schulverwaltungsprogramm SchILD NRW in Kombination mit Untis verwendet wird.
Für andere Software gibt es aktuell noch keine Import-Programme, das ICC stellt jedoch ausreichend Schnittstellen zur Verfügung,
um eigene Import-Programme zu verwenden.

## Mitmachen

Mitmachen ist ausdrücklich erwünscht - Bugmeldungen, Funktionswünsche und Pullrequests sind immer herzlich willkommen. 
Ein GitHub Account ist erforderlich. 

## Namespaces

Da das Projekt recht groß ist und die Symfony-Konventionen für Namespaces nicht mehr sinnvoll umsetzbar waren, wurde
das Projekt bzw. die Namespaces nach folgender Konvention umstrukturiert:

* `Framework`-Namespace: Hier sind Hilfsklassen untergebracht, die z.T. auch von anderen Modulen des ICCs genutzt werden. Klassen in diesem Namespace haben keine Abhängigkeiten zu den restlichen Namespaces im Projekt.
* `Infrastructure`-Namespace: Namespace für sämtlichen Glue-Code für 3rd party Bibliotheken. Klassen in diesem Namespace haben höchstens Abhängigkeiten zum `Common`-Namespace.
* `Common`-Namespace: Namespace für alle grundlegenden Entitäten etc. Klassen in diesem Namespace haben keine Abhängigkeiten zu den restlichen Namespaces, abgesehen von `Framework`.
* Alle restlichen Namespaces tragen den Namen des Features. Sowohl hier als auch im `Common`-Namespace werden Symfony-Konventionen für Namespaces genutzt.

## Lizenz

[AGPL-3.0](LICENSE)

