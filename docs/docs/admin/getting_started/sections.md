---
sidebar_position: 1
---

# Schuljahresabschnitte

Damit das ICC vernünftig arbeiten kann und Importe funktionieren, muss zunächst das Schuljahr (mit seinen Halbjahren)
angelegt werden. 

Dazu unter *Verwaltung ➜ Datenverwaltung ➜ Abschnitte* die entsprechenden Abschnitte anlegen.

Arbeitet man mit SchILD NRW, so entsprechen Jahr und Nummer jeweils dem Jahr und dem Abschnitt aus SchILD NRW (also bspw.
Jahr 2022 und Nummer 1 entspricht dem ersten Halbjahr des Schuljahres 2022/23).

## Aktuellen Abschnitt festlegen

Anschließend muss unter *Einstellungen ➜ Allgemein* der aktuelle Abschnitt festgelegt werden.

## Fallback-Schuljahresabschnitt

Möchte man bereits während des ersten Halbjahres Termine für das zweite Halbjahr erstellen, so muss unter *Einstellungen
➜ Import* der Fallback-Schuljahresabschnitt auf das erste Halbjahr gesetzt werden. Anderenfalls meckert das System beim
Import über die API-Schnittstelle, dass Lerngruppen nicht vorhanden sind.