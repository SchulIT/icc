# Sicherheit

## Datenbankverschlüsselung

Zur Sicherheit werden einige sensible Daten auf dem ICC verschlüsselt in der Datenbank gespeichert. Darunter zählen:

* private Nachrichten (sowohl der Titel als auch alle Nachrichten)
* Abwesenheitsmeldungen von Lernenden und Lehrkräften
* Lernplattform-Initialpasswörter von Lernenden

Diese Werte werden automatisch beim Schreiben in die Datenbank verschlüsselt und beim Abrufen entschlüsselt. 

Der zugrundeliegende Schlüssel muss dem ICC zur Ver- und Entschlüsselung vorliegen und ist daher in der Konfigurationsdatei
abgespeichert. 

:::warning Wichtig
Sofern ein Angreifer sowohl Zugriff auf die Konfigurationsdatei als auch auf die Datenbank besitzt, können die Daten
entschlüsselt werden. Die Verschlüsselung ist nur effektiv gegenüber Angriffen, die sich ausschließlich auf die Datenbank
konzentrieren.
:::

## Verschlüsselung von Noten

Noten der Lernenden werden bereits clientseitig verschlüsselt, sodass eine Entschlüsselung nur möglich ist, wenn das Notenpasswort
bekannt ist. Auf dem Server verweilt nur der verschlüsselte Schlüssel, der zum Ver- und Entschlüsseln verschlüsselt in den Browser
übertragen wird und von der Lehrkraft mithilfe eines gemeinsamen Passwortes entschlüsselt wird.