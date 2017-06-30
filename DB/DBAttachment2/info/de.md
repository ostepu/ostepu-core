Die DBAttachment2 ermöglicht den Zugriff auf die `Attachment_X` Tabellen der Datenbank. Dazu wird bei einem `POST /course` Aufruf die nachstehende Tabelle erzeugt.

## Eingänge
---------------

|||
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente aus der Veranstaltung|
|Befehl| delete<br>(/:pre)/course|
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
|Beschreibung| gibt die Anhänge der Veranstaltung zurück|
|Befehl| get<br>(/:pre)/attachment/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| fügt einen neuen Eintrag hinzu|
|Befehl| post<br>(/:pre)/attachment|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| editiert einen einzelnen Anhang|
|Befehl| put<br>(/:pre)/attachment(/attachment)/:aid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| ermittelt alle Anhänge einer Veranstaltung|
|Befehl| get<br>(/:pre)/attachment/exercise/:eid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| gibt eine einzelne Veranstaltung aus|
|Befehl| get<br>(/:pre)/attachment(/attachment)/:aid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| ermittelt alle Anhänge einer Übungsserie|
|Befehl| get<br>(/:pre)/attachment/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| entfernt einen einzelnen Anhang|
|Befehl| delete<br>(/:pre)/attachment(/attachment)/:aid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| installiert die Komponente in die Veranstaltung|
|Befehl| post<br>(/:pre)/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||out|
| :----------- |:-----: |
|Ziel| DBQuery2|
|Befehl| POST<br>/query|
|Beschreibung| über diesen Ausgang werden die Anfragen an die Datenbank gestellt|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBAttachment2 als lokales Objekt aufgerufen werden kann|


Stand 30.06.2017
