## Eingänge
---------------

|||
| :----------- |:-----: |
|Befehl| GET<br>/link/exists/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| post<br>/process|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| post<br>/course|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| delete<br>/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||formDb|
| :----------- |:-----: |
|Ziel| DBForm|
|Befehl| GET<br>/form/exercise/:exerciseid|
|Beschreibung| für den Befehl formDb|

||pdf|
| :----------- |:-----: |
|Ziel| FSPdf|
|Befehl| POST<br>/pdf|
|Beschreibung| für den Befehl pdf|

||postProcess|
| :----------- |:-----: |
|Ziel| DBProcessList|
|Befehl| POST<br>/process|
|Beschreibung| für den Befehl postProcess|

||deleteProcess|
| :----------- |:-----: |
|Ziel| DBProcessList|
|Befehl| DELETE<br>/process/process/:processid|
|Beschreibung| für den Befehl deleteProcess|

||getProcess|
| :----------- |:-----: |
|Ziel| DBProcessList|
|Befehl| GET<br>/process/course/:courseid/component/:componentid|
|Beschreibung| für den Befehl getProcess|


## Anbindungen
---------------

|Ausgang|postCourse|
| :----------- |:-----: |
|Ziel| LForm|

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LFormPredecessor als lokales Objekt aufgerufen werden kann|

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CHelp|
|Beschreibung| hier werden Hilfedateien beim zentralen Hilfesystem angemeldet, sodass sie über ihre globale Adresse abgerufen werden können|


Stand 30.06.2017
