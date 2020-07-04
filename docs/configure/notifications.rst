Benachrichtigungen
==================

Das ICC unterstützt sowohl Benachrichtigungen via E-Mails als auch Web Push (letzteres ist experimentell). Diese sind
grundsätzlich deaktiviert und müssen zunächst durch den Administrator freigeschaltet werden.

Die Einstellungen können im Einstellungen-Menü :fa:`wrench` unter Benachrichtigungen vorgenommen werden.

.. warning:: Diese Einstellungen legen zum einen fest, wer Benachrichtigungen aktivieren kann.

.. warning:: Diese Einstellungen werden zusätzlich vor dem Versenden überprüft, sodass Benachrichtigungen mit diesen Einstellungen deaktiviert werden können.

Push-Benachrichtigungen
-----------------------

Über die `Push API <https://www.w3.org/TR/push-api/Overview.html>`_ können Push-Benachrichtigungen an Browser gesendet
werden. Aktuell funktioniert dies mit folgenden Browsern, sowohl auf dem Desktop als auch in den mobilen Varianten:

- Google Chrome
- Mozilla Firefox
- Microsoft Edge
- Opera
- Samsung Internet

Eine Übersicht der unterstützten Browser bietet `Mozilla <https://developer.mozilla.org/en-US/docs/Web/API/Push_API#Browser_compatibility>`_ (Englisch).

Unter "Push-Benachrichtigungen aktiviert für" gibt man die Benutzergruppen an, für die Push-Benachrichtigungen freigeschaltet
sind.

E-Mail-Benachrichtigungen
-------------------------

Benachrichtigungen können über E-Mail versendet werden. Auch hier muss man angeben, welche Benutzergruppen für E-Mail-Benachrichtigungen
freigeschaltet sind.

.. warning:: E-Mail-Benachrichtigungen sollten nur für eine kleine Benutzergruppe freigeschaltet werden, da viele Anbieter das Versenden von Massen-E-Mails verhindern.