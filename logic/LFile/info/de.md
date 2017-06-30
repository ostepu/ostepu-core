## Eingänge
---------------

|||
| :----------- |:-----: |
|Befehl| POST<br>/file(/)|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| DELETE<br>/file/:fileid(/)|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||fileDb|
| :----------- |:-----: |
|Ziel| DBFile|
|Befehl| GET<br>/file/hash/:hash|
|Beschreibung| für den Befehl fileDb|

||fileDb|
| :----------- |:-----: |
|Ziel| DBFile|
|Befehl| DELETE<br>/file/:fileid|
|Beschreibung| für den Befehl fileDb|

||file|
| :----------- |:-----: |
|Ziel| FSFile|
|Befehl| POST<br>/file|
|Beschreibung| für den Befehl file|

||file|
| :----------- |:-----: |
|Ziel| FSFile|
|Befehl| DELETE<br>/file/:adress|
|Beschreibung| für den Befehl file|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LFile als lokales Objekt aufgerufen werden kann|


Stand 30.06.2017
