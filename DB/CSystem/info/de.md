die CSystem bietet Anfragen an, welche eher allgemeine Aufgaben haben oder anderen Komponenten dienen sollen

## Eingänge
---------------

||getTimestamp|
| :----------- |:-----: |
|Beschreibung| gibt den aktuellen UNIX-Zeitstempel zurück|
|Befehl| GET<br>/timestamp|
|Eingabetyp| -|
|Ausgabetyp| -|

||getApiProfiles|
| :----------- |:-----: |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET<br>/api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit CSystem als lokales Objekt aufgerufen werden kann|

|Ausgang|getComponentProfiles|
| :----------- |:-----: |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 30.06.2017
