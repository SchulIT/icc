Klausurplan
===========

Die Einstellungen zum Klausurplan sind wichtig, um die Sichtbarkeit des Klausurplans einzuschränken und die Erstellung
von Klausuren zu konfigurieren.

Die Einstellungen können im Einstellungen-Menü :fa:`wrench` unter Klausurplan vorgenommen werden.

Allgemeine Informationen
------------------------

Sichtbar für
############

Hier wird festgelegt, welche Benutzergruppen den Klausurplan grundsätzlich einsehen können.

Vorausblick für Lernende
########################

Hier wird festgelegt, wie weit Lernende beim Klausurplan in die Zukunft schauen können. Der Wert ``0`` bedeutet dabei,
dass Lernende beliebig weit in die Zukunft blicken können, d.h. sie sehen alle Klausurtermine. Ist der Wert größer als ``0``,
so sehen die Lernenden nur die Klausuren der nächsten N Tage.

Vorausblick der Aufsichten für Lernende
#######################################

Hier wird festgelegt, ab welchem Zeitpunkt Lernende die Aufsichten ihrer Klausuren einsehen können. Der Wert ``0`` bedeutet dabei,
dass Lernende immer die Aufsichten sehen können. Ist der Wert größer als ``0``, so sehen die Lernenden nur Aufsichten von
Klausuren der nächsten N Tage.

Planung
-------

In dieser Sektion werden Parameter eingetragen, die bei der Planung von Klausuren interessant ist. Dies ist nur notwendig,
wenn Klausuren über das ICC geplant werden sollen.

Klausuren pro Woche
###################

Gibt die maximale Anzahl an Klausuren pro Woche an. Das System prüft bei der Erstellung einer Klausur dann, dass dieser
Wert bei allen Lernenden der betroffenen Lerngruppe eingehalten wird.

Klausuren pro Tag
#################

Gibt die maximale Anzahl an Klausuren pro Tag an. Das System prüft bei der Erstellung einer Klausur dann, dass dieser
Wert bei allen Lernenden der betroffenen Lerngruppe eingehalten wird.

Benachrichtigungen
------------------

Benachrichtiungen aktiviert
###########################

Diese Option legt fest, ob Benachrichtigungen beim Import eines neuen Klausurplans versendet
werden sollen. Diese Einstellung betrifft sowohl `Push- als auch E-Mail-Benachrichtigungen <notifications.html>`_.

.. warning:: Es werden keine Benachrichtigungen versendet, wenn eine Klausur nur über das ICC angelegt wird.

Absender
########

Dieser Name wird als Absender von E-Mail Benachrichtigungen angezeigt.

Anwort-E-Mail-Adresse
#####################

Standardmäßig werden E-Mails von einem noreply-Konto versendet. Mit der Angabe einer E-Mail-Adresse hier ist es möglich,
dass Antwort-E-Mails an eine andere Adresse gesendet werden, bspw. an den Klausurplaner.