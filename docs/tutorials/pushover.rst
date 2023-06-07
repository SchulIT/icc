Pushover-Integration
====================

Mithilfe von `Pushover <https://www.pushover.net>`_ können Push-Benachrichtigungen versendet werden. Dazu müssen die
Empfänger die (kostenpflichtige) Pushover-App herunterladen. Stand Juni 2023 handelt es sich um eine einmalige
Zahlung pro Betriebssystem.

Integration aktivieren (Administrator)
--------------------------------------

Um die Integration zu aktivieren muss zunächst ein API Token auf der Pushover-Seite erzeugt werden. Dieses kann in den
:fa:`wrench` Einstellungen unter Benachrichtigungen eingetragen werden. Dort kann dann außerdem eingetragen werden,
für welche Benutzergruppen die Art der Benachrichtigung freigeschaltet ist.

Benachrichtigungen aktivieren (Benutzer)
----------------------------------------

Möchte ein Benutzer über Pushover benachrichtigt werden, so muss er in den Profileinstellungen (Benutzer-Icon -> Profil
-> Benachrichtigungen) den persönlichen Pushover-Token eingetragen. Dieser kann in der App ausgelesen werden.

.. info:: Es werden keine Klarnamen nach Pushover übertragen. Es wird genau derselbe Betreff und Inhalt übertragen, wie auf der Benachrichtigungen-Seite zu sehen ist.