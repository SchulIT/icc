---
sidebar_position: 1
---

# Kiosk-Benutzer

Kiosk-Benutzer sind spezielle Benutzer, die nur lesend auf das ICC zugreifen können. Die Einrichtung des Benutzers erfolgt
dabei im Single Sign-On (siehe [Handbuch](https://docs.schulit.de/idp)). Es handelt sich um einen Benutzer vom Typ `Benutzer`. Um die Leseberechtigungen
zu definieren, kann - je nach Bedürfnissen - folgende Rollen zugewiesen werden, um Lesezugriff auf gewisse Bereiche festzulegen:

* ROLE_RESOURCE_RESERVATION_VIEWER
* ROLE_RESOURCE_RESERVATION_CREATOR (falls man über den Benutzer Reservierungen durchführen lassen möchte)
* ROLE_MESSAGE_VIEWER
* ROLE_DOCUMENT_VIEWER
* ROLE_APPOINTMENT_VIEWER
* ROLE_LISTS_VIEWER

:::danger Wichtig
Es darf keine weitere Rolle spezifiziert sein, da der Benutzer möglicherweise nicht mehr im Lese-Modus unterwegs ist.
:::