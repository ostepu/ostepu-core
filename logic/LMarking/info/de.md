## Eingänge
---------------

|||
| :----------- |:-----: |
|Befehl| POST<br>/marking|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| DELETE<br>/marking/marking/:markingid|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||file|
| :----------- |:-----: |
|Ziel| LFile|
|Befehl| POST<br>/file|
|Beschreibung| für den Befehl file|

||marking|
| :----------- |:-----: |
|Ziel| DBMarking|
|Befehl| DELETE<br>/marking/marking/:markingid|
|Beschreibung| für den Befehl marking|

||marking|
| :----------- |:-----: |
|Ziel| DBMarking|
|Befehl| POST<br>/marking|
|Beschreibung| für den Befehl marking|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LMarking als lokales Objekt aufgerufen werden kann|


Stand 30.06.2017
