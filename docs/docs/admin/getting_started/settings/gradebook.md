---
sidebar_position: 9
---

# Notenmodul-Einstellungen

Um das Notenmodul nutzen zu können, muss zunächst ein Hauptschlüssel generiert werden. Mit diesem Schlüssel werden
die Noten (im Browser) ver- und entschlüsselt. 

:::tip Gewusst
Noten werden **zu keiner Zeit unverschlüsselt** auf dem Server verarbeitet. Das Ver- und Entschlüsseln erfolgt im
Browser und setzt daher moderne Browser voraus, die die entsprechenden [Web Crypto API](https://developer.mozilla.org/en-US/docs/Web/API/Web_Crypto_API)
-Funktionen bereitstellen.
:::

## Schlüsselerzeugung

Zunächst legt man das Passwort fest, mit dem die Lehrkräfte später die Noten ver- bzw. entschlüsseln. Dieses trägt man
unter *Passwort für Noten* ein. Anschließend klickt man auf den Button *Hauptschlüssel erzeugen*. 

Dadurch wird **im Browser** ein Hauptschlüssel erstellt, der nur mithilfe des Passwortes verwendet werden kann. Der
Hauptschlüssel wird oben unter *Hauptschlüssel* eingefügt und ist ein JSON. 

Damit man nicht aus Versehen den Schlüssel verändert, muss - auch bei der initialen Schlüsselerstellung - die Checkbox
*Schlüsseländerung bestätigen* aktiviert werden.

:::tip Gewusst
Aufgrund der kryptografischen Eigenschaft des Schlüssels wird jedesmal ein anderes JSON erzeugt, wenn man auf *Hauptschlüssel
erzeugen* klickt.
:::

## Zwischenspeichern des Schlüssels

Damit Lehrkräfte nicht bei jedem neuen Seitenabruf das Notenpasswort eingeben müssen, kann außerdem eine Anzahl an Sekunden
festgelegt werden, für die das Passwort **lokal im Browser** zwischengespeichert wird, sodass die erneute Eingabe nicht
erfolgen muss.

:::tip Gewusst
Diese Funktion klappt nicht über mehrere Browsertabs hinweg. Das Passwort wird sozusagen nur im Kontext des aktuellen
Tabs gespeichert.
:::