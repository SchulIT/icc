---
sidebar_position: 8
---

# Unterrichtsbuch

Hier können Einstellungen für das Unterrichtsbuch vorgenommen werden.

Die Einstellungen *Klassenleitung entschuldigt* und *Fachlehrkraft entschuldigt* sind dabei entsprechend erklärt.

Über die Einstellung *Lernende mit folgendem Status nicht berücksichtigen* kann konfiguriert werden, welche Lernende
bei der Anwesenheitskontrolle ignoriert werden sollen. Dabei wird der Status der Lernenden entsprechend verglichen. 
Nutzt man SchILD NRW, so sollte hier *Abgaenger* eingtragen werden.

## Schriftart

Da der PDF-Export standardmäßig keine Unicode-Zeichen unterstützt, muss unbedingt eine Schriftart hinterlegt werden. Diese
wird dann für den PDF-Export genutzt.

:::tip Gewusst
Aus Lizenzgründen wird keine Schriftart mitgeliefert.
:::

### Schriftart aus Windows exportieren

Wenn man z.B. die Schriftart Calibri verwenden möchte, geht man folgendermaßen vor. Auf einem Windows-PC öffnet man im
Windows Explorer den Pfad `C:\Windows\Fonts`. Anschließend die gewünschte Schriftart (bspw. Calibri) auswählen und kopieren
(über das Kontextmenü oder Strg+C):

![](/img/docs/export-windows-font.png)

Anschließend fügt man die Dateien in einen beliebigen Ordner ein und man erhält einen Haufen von TTF-Dateien. Darin muss
nun die Standard-Schriftbreite und die Schriftbreite "fett" gefunden werden (`calibri.ttf` bzw. `calibrib.ttf`). Diese
müssen nun hinterlegt werden:

![](/img/docs/import-calibri.png)

:::success Erledigt
Ab sofort unterstützt der PDF-Export auch Unicode-Zeichen. Der Upload muss nur einmal erfolgen.
:::