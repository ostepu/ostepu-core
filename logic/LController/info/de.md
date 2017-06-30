## Eingänge
---------------

|||
| :----------- |:-----: |
|Befehl| POST<br>/:string+|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| PUT<br>/:string+|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/:string+|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| DELETE<br>/:string+|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||database|
| :----------- |:-----: |
|Ziel| DBControl|
|Befehl| POST<br>/path+|
|Beschreibung| für den Befehl database|

||database|
| :----------- |:-----: |
|Ziel| DBControl|
|Befehl| GET<br>/path+|
|Beschreibung| für den Befehl database|

||database|
| :----------- |:-----: |
|Ziel| DBControl|
|Befehl| PUT<br>/path+|
|Beschreibung| für den Befehl database|

||database|
| :----------- |:-----: |
|Ziel| DBControl|
|Befehl| DELETE<br>/path+|
|Beschreibung| für den Befehl database|

||filesystem|
| :----------- |:-----: |
|Ziel| FSControl|
|Befehl| POST<br>/path+|
|Beschreibung| für den Befehl filesystem|

||filesystem|
| :----------- |:-----: |
|Ziel| FSControl|
|Befehl| GET<br>/path+|
|Beschreibung| für den Befehl filesystem|

||filesystem|
| :----------- |:-----: |
|Ziel| FSControl|
|Befehl| PUT<br>/path+|
|Beschreibung| für den Befehl filesystem|

||filesystem|
| :----------- |:-----: |
|Ziel| FSControl|
|Befehl| DELETE<br>/path+|
|Beschreibung| für den Befehl filesystem|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LController als lokales Objekt aufgerufen werden kann|


Stand 30.06.2017
