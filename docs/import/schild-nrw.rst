SchILD NRW
==========

Der Import aus SchILD NRW erfolgt mithilfe des `SchILD ICC Importer <https://github.com/schulit/schild-icc-importer>`_.

Installation und Konfiguration
------------------------------

Hinweise zur Konfiguration und Installation des Programmes gibt es auf `GitHub <https://github.com/schulit/schild-icc-importer>`_.

Importvorgang
-------------

Dazu die gewünschten Optionen und das aktuelle Schuljahr auswählen und auf "Import starten" klicken.

.. image:: ../images/schild-icc-importer.png

.. warning:: Aktuell unterstützt das ICC keine Schuljahresabschnitte. Es kann immer nur der aktuelle Abschnitt hochgeladen werden.


Fehlerbehebung
--------------

Wechsel Klassen- und Kursunterricht
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Wenn ein Klassenunterricht zu einem Kursunterricht umgewandelt wird (oder umgekehrt), kommt es zu 
Problemen beim Import, weil das Tool diese Änderung nicht sieht und stattdessen den alten Unterricht
löscht und einen neuen anlegen möchte. Das wird jedoch verhindert, sobald man Unterrichtsbücher verwendet.

Damit das System den neuen Unterricht wiedererkennt, muss die externe ID händisch angepasst werden.

Fall 1: Aus Klassen- wird Kursunterricht
########################################

Zunächst muss man in der SchILD-Datenbank die Kurs ID herausfinden. Es handelt sich dabei um den Wert 
der Spalte `ID` des zugehörigen Datensatzes in der Tabelle `Kurse`.

Diese muss nun im ICC unter Verwaltung -> EasyAdmin -> Tuition -> `Kurs suchen` -> Ändern unter ExternalID
eingetragen werden.

Anschließend funktioniert der Import wieder.

Fall 2: Aus Kurs- wird Klassenunterricht
########################################

Die externe ID für Klassenunterrichte wird gebildet aus dem Fachkürzel und dem Namen der Lerngruppe, diese ist 
bei Klassenunterrichten stets gleich dem Namen der Klasse. Bei mehreren Klassen werden diese mit einem Bindestrich
kombiniert. Beispiele:

- Mathe, 6c: M-06C
- Informatik, 6c und 6d: IF-06C-06D

