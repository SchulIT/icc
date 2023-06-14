Benutzerrollen
==============

ROLE_USER
#########

Diese Rolle muss jeder Benutzer haben und besitzt keine besonderen Zugriffsrechte.

ROLE_MESSAGE_CREATOR
####################

Nutzer mit dieser Rolle dürfen Mitteilungen erstellen und eigene Mitteilungen ändern und löschen.

ROLE_MESSAGE_PRIORITY
#####################

Nutzer mit dieser Rolle dürfen die Priorität von Mitteilungen festlegen.

ROLE_MESSAGE_ADMIN
##################

Diese Rolle beinhaltet die Rollen ``ROLE_MESSAGE_CREATOR`` und ``ROLE_MESSAGE_PRIORITY`` und erlaubt darüber hinaus,
fremde Mitteilungen zu bearbeiten und zu löschen.

ROLE_DOCUMENTS_ADMIN
####################

Nutzer mit dieser Rolle dürfen Dokumente verwalten.

ROLE_APPOINTMENT_CREATOR
########################

Nutzer mit dieser Rolle dürfen Termine eintragen. Diese besitzen zunächst den Status "nicht bestätigt" und werden für
Lernende und Eltern nicht angezeigt. Andere Benutzer (u.a. Lehrkräfte) sehen unbestätigte Termine mit einem entsprechenden
Hinweis. Nutzer mit dieser Rolle können nur ihre eigenen Termine bearbeiten und löschen.

Benutzer mit der Rolle ``ROLE_APPOINTMENTS_ADMIN`` können Termine bestätigen.

ROLE_APPOINTMENTS_ADMIN
#######################

Nutzer mit dieser Rolle dürfen alle Termine verwalten und Termine bestätigen. Durch diese Nutzer erstellte Termine besitzen
automatisch den Status "bestätigt".

ROLE_WIKI_ADMIN
###############

Nutzer mit dieser Rolle dürfen Wiki-Artikel verwalten.

ROLE_EXAMS_CREATOR
##################

Nutzer mit dieser Rolle dürfen Klausuren erstellen.

ROLE_EXAMS_ADMIN
################

Diese Rolle beinhaltet die Rolle ``ROLE_EXAMS_CREATOR`` und erlaubt darüber hinaus, alle Klausuren zu bearbeiten und zu löschen.

ROLE_BOOK_VIEWER
################

Nutzer mit dieser Rolle haben lesenden Zugriff auf das Klassenbuch.

**Achtung:** Diese Rolle ist noch nicht vollständig implementiert und darf daher nicht verwendet werden.

ROLE_BOOK_ENTRY_CREATOR
#######################

Diese Rolle beinhaltet die Rolle ``ROLE_BOOK_VIEWER`` und erlaubt darüber hinaus, Klassenbucheinträge oder Entschuldigungen einzutragen.

ROLE_STUDENT_ABSENCE_VIEWER
###########################

Nutzer mit dieser Rolle haben Zugriff auf alle Abwesenheitsmeldungen und können diese editieren und löschen.

ROLE_STUDENT_ABSENCE_CREATOR
############################

Diese Rolle erlaubt es, Abwesenheitsmeldungen für beliebige Lernende zu erstellen.

ROLE_STUDENT_ABSENCE_APPROVER
#############################

Diese Rolle erlaubt es, Abwesenheitsmeldungen zu genehmigen (sofern diese genehmigt werden müssen). Standardmäßig
können kann nur die Klassenleitung eine Abwesenheitsmeldung genehmigen.

ROLE_KIOSK
##########

Nutzer mit dieser Rolle haben nur lesenden Zugriff auf das ICC.

ROLE_ADMIN
##########

Diese Rolle beinhaltet die Rollen ``ROLE_WIKI_ADMIN``, ``ROLE_DOCUMENTS_ADMIN``, ``ROLE_MESSAGE_ADMIN``, ``ROLE_APPOINTMENTS_ADMIN`` und ``ROLE_EXAMS_ADMIN``.
Sie erlaubt darüber hinaus, das System zu administrieren.

ROLE_SUPER_ADMIN
################

Diese Rolle beinhaltet die Rolle ``ROLE_ADMIN`` und erlaubt darüber hinaus, das Log anzuzeigen und tiefgreifende Einstellungen am System
vorzunehmen.

ROLE_SHOW_BIRTHDAY
##################

Benutzer mit dieser Rolle können Geburtstage einsehen. Lernende und Eltern können dabei jedoch nur die eigenen Geburtstage anzeigen.
Alle anderen Benutzertypen können Geburtstage aller Lernenden bzw. aller Lehrkräfte sehen.

ROLE_CRON
#########

Diese Rolle darf nicht vergeben werden. Sie wird dem einzigen Cronjob-Benutzer automatisch zugewiesen.