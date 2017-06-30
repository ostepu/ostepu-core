Die DBNotification ermöglicht den Zugriff auf die `Notification_X` Tabellen der Datenbank. Diese verwalten veranstaltungsbezogene Meldungen. Diese werden durch Dozenten und Admins in der Kursverwaltung erstellt und den Nutzern auf deren Veranstaltungsübersichten angezeigt. Dazu wird bei einem `POST /course` Aufruf die nachstehende Tabelle erzeugt.

## Eingänge
---------------

|||
| :----------- |:-----: |
|Beschreibung| installiert die Komponente in die übermittelte Veranstaltung|
|Befehl| post<br>(/:pre)/course|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| prüft, ob die Komponente korrekt in die Veranstaltung installiert wurde|
|Befehl| get<br>(/:pre)/link/exists/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| gibt einen einzelnen Eintrag anhand dessen ID zurück|
|Befehl| get<br>(/:pre)/notification/notification/:notid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| fügt einen neuen Eintrag ein|
|Befehl| post<br>(/:pre)/notification/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| editiert einen einzelnen Eintrag|
|Befehl| put<br>(/:pre)/notification/notification/:notid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| ermittelt alle 'aktiven' Meldungen einer Veranstaltung (also keine abgelaufenen Meldungen)|
|Befehl| get<br>(/:pre)/notification/alive/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| ermittelt alle Meldungen einer Veranstaltung|
|Befehl| get<br>(/:pre)/notification/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente aus einer Veranstaltung|
|Befehl| delete<br>(/:pre)/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| entfernt eine einzelne Meldung|
|Befehl| delete<br>(/:pre)/notification/notification/:notid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| installiert die Komponente in die Plattform|
|Befehl| post<br>(/:pre)/platform|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||out|
| :----------- |:-----: |
|Ziel| DBQuery2|
|Befehl| POST<br>/query|
|Beschreibung| über diesen Ausgang werden alle Datenbankanfragen ausgeführt|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBNotification als lokales Objekt aufgerufen werden kann|

|Ausgang|postCourse|
| :----------- |:-----: |
|Ziel| LCourse|
|Beschreibung| wenn eine neue Veranstaltung angelegt wird, dann wollen wir auch aufgerufen werden|

|Ausgang|deleteCourse|
| :----------- |:-----: |
|Ziel| LCourse|
|Beschreibung| wenn eine Veranstaltung gelöscht wird, dann müssen auch unsere Tabellen entfernt werden|

|Ausgang|postPlatform|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|


Stand 30.06.2017
