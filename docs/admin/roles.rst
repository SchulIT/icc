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

ROLE_APPOINTMENTS_ADMIN
#######################

Nutzer mit dieser Rolle dürfen Termine verwalten.

ROLE_WIKI_ADMIN
###############

Nutzer mit dieser Rolle dürfen Wiki-Artikel verwalten.

ROLE_EXAMS_CREATOR
##################

Nutzer mit dieser Rolle dürfen Klausuren erstellen.

ROLE_EXAMS_ADMIN
################

Diese Rolle beinhaltet die Rolle ``ROLE_EXAMS_CREATOR`` und erlaubt darüber hinaus, alle Klausuren zu bearbeiten und zu löschen.

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

ROLE_CRON
#########

Diese Rolle darf nicht vergeben werden. Sie wird dem einzigen Cronjob-Benutzer automatisch zugewiesen.