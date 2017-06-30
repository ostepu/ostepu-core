Die DBProcess ermöglicht den Zugriff auf die `Process_X` Tabellen der Datenbank, dabei stellen diese Einträge ein Vorgang dar. Es werden aufzurufende Komponenten mit Aufrufparametern versehen. Dazu wird bei einem `POST /course` Aufruf die nachstehende Tabelle erzeugt.

## Eingänge
---------------

|||
| :----------- |:-----: |
|Beschreibung| fragt alle Einträge zur gegeben Übungsserie ab|
|Befehl| get<br>(/:pre)/process/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| entfernt einen Eintrag anhand seiner ID|
|Befehl| delete<br>(/:pre)/process(/process)/:processid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| ermittet alle Einträge zu einer Veranstaltung|
|Befehl| get<br>(/:pre)/process/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| editiert einen Eintrag anhand seiner ID|
|Befehl| put<br>(/:pre)/process(/process)/:processid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| installiert die Komponente in die gegebene Veranstaltung|
|Befehl| post<br>(/:pre)/course|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| gibt einen einzelnen Eintrag anhand seiner ID zurück|
|Befehl| get<br>(/:pre)/process(/process)/:processid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| fügt einen neuen Eintrag ein|
|Befehl| post<br>(/:pre)/process|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| prüft, ob die Komponente korrekt in diese Veranstaltung installiert wurde/ist|
|Befehl| get<br>(/:pre)/link/exists/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente aus der Veranstaltung|
|Befehl| delete<br>(/:pre)/course(/course)/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| ermittelt alle Einträge anhand der ID einer Übungsaufgabe|
|Befehl| get<br>(/:pre)/process/exercise/:eid|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||out|
| :----------- |:-----: |
|Ziel| DBQuery2|
|Befehl| POST<br>/query|
|Beschreibung| über diesen Ausgang werden alle Anfragen an die Datenbank gestellt|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBProcess als lokales Objekt aufgerufen werden kann|


Stand 30.06.2017
