---
sidebar_position: 2
---

# Accounts

## Arten von Accounts

Das System kennt folgende Arten von Accounts:

* Lehrkraft
* Schülerin/Schüler
* Elternteil
* Mitarbeiter (z.B. für Sekretariat oder Gebäudemanagement)
* Praktikant
* Benutzer (z.B. für Administratoren)

## Wiedererkennung von Schülerinnen und Schülern sowie Lehrkräften

Das ICC pflegt eine Datenbank mit allen Schülerinnen und Schülern sowie allen Lehrkräften. Bei der Anmeldung am ICC wird
dazu die E-Mail-Adresse (bzw. bei Eltern ggf. mehrere E-Mail-Adressen, wenn mehrere Kinder an der Schule sind) abgeglichen
und entsprechend der Benutzeraccount mit dem Schüler, der Schülerin oder der Lehrkraft verknüpft.

:::warning Wichtig
Damit die Verknüpfung funktioniert, ist es äußerst wichtig, dass die E-Mail-Adressen im ICC und im Single Sign-On
übereinstimmen. Anderenfalls kann eine Verknüpfung nicht stattfinden und man erhält ggf. keinen Zugriff auf das ICC.
:::

:::tip Gewusst
Grundsätzlich ist es nicht möglich, dass sich Lehrkräfte mit Schüleraccounts verknüpfen. Dies ist auch eigentlich nicht
notwendig, da alle Lehrkräfte dieselben Rechte eingeräumt werden wie Eltern (z.B. beim Erstellen von Abwesenheitsmeldungen).
Lesend können Lehrkräfte immer auf alle Schülerinnen und Schüler zugreifen (z.B. deren Stundenpläne, ...).
:::

## Deaktivierung von Accounts

Die Deaktivierung von Accounts erfolgt grundsätzlich über das Single Sign-On.

## Löschen von Accounts

Accounts vom Typ `student`, `parent` und `teacher` werden automatisch bereinigt, wenn sie keinem Schüler/Schülerin bzw.
keiner Lehrkraft mehr zugeordnet sind. Es ist daher nicht notwendig, die Benutzeraccounts händisch zu löschen.

:::tip Gewusst
Eine Lehrkraft bzw. ein Schüler oder eine Schülerin werden nur dann bereinigt, wenn sie (a) nicht mehr existieren oder (b)
wenn sie keinem Abschnitt zugeordnet sind.
:::

:::warning Wichtig
Das Löschen setzt voraus, dass [Cronjobs](../admin/maintenance/cronjobs) konfiguriert sind.
:::