Die DBAttachment ermöglicht den Zugriff auf die `Attachment` Tabelle der Datenbank. Diese verwaltet Anhänge für Aufgaben (`Exercise`). Dazu wird bei einem `POST /course` Aufruf die nachstehende Tabelle erzeugt.

## Eingänge
---------------

|||
| :----------- |:-----: |
|Beschreibung| ermittelt alle Anhänge einer Übungsserie|
|Befehl| get<br>/attachment/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| editiert einen Anhang|
|Befehl| put<br>/attachment(/attachment)/:aid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| liefert einen einzelnen Anhang zurück|
|Befehl| get<br>/attachment(/attachment)/:aid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| entfernt die Anhänge einer Aufgabe|
|Befehl| delete<br>/attachment/exercise/:eid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente aus der Plattform|
|Befehl| DELETE<br>/platform|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| löscht einen Anhang anhand der Aufgabe und der Datei|
|Befehl| delete<br>/attachment/exercise/:eid/file/:fileid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| fügt einen Anhang ein|
|Befehl| post<br>/attachment|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| ermittelt alle Anhänge zu einer Aufgabe|
|Befehl| get<br>/attachment/exercise/:eid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| entfernt einen Anhang|
|Befehl| delete<br>/attachment(/attachment)/:aid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| installiert die Komponente in die Plattform|
|Befehl| POST<br>/platform|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| liefert alle Anhänge der Plattform|
|Befehl| get<br>/attachment(/attachment)|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| prüft, ob die Komponente korrekt in die Plattform installiert wurde|
|Befehl| GET<br>/link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||out|
| :----------- |:-----: |
|Ziel| DBQuery|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl out|

||out2|
| :----------- |:-----: |
|Ziel| DBQuery2|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl out2|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBAttachment als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|


Stand 30.06.2017
