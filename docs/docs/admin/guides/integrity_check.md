---
sidebar_position: 11
---

# Integritätscheck

Der Integritätscheck kann fehlerhafte Eingaben im Hinblick auf die Anwesenheitskontrolle überprüfen. Folgende Fehler
werden aktuell überprüft:

* Kind ist anwesend, obwohl es eine Abwesenheitsmeldung gibt
* Kind ist parallel in mehreren Unterrichten anwesend
* Anwesenheitsstatus wechsels mehr als einmal täglich

Diese Checks benötigen ca. eine halbe Sekunde pro Kind und sollten daher im Hintergrund laufen. Daher sollte ein entsprechender
[Hintergrunddienst](../maintenance/background_jobs) eingerichtet werden. 