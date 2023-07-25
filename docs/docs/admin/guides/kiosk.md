---
sidebar_position: 1
---

# Kiosk-Benutzer

Kiosk-Benutzer sind spezielle Benutzer, die nur lesend auf das ICC zugreifen können. Die Einrichtung des Benutzers erfolgt
dabei im Single Sign-On (siehe [Handbuch](https://docs.schulit.de/idp)). Dem Benutzer muss die Rolle `ROLE_KIOSK` zugewiesen sein.

:::danger Wichtig
Es darf keine weitere Rolle spezifiziert sein, da der Benutzer möglicherweise nicht mehr im Lese-Modus unterwegs ist.
:::