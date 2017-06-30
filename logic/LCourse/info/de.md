## Eingänge
---------------

|||
| :----------- |:-----: |
|Befehl| post<br>/course|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| put<br>/course/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| delete<br>/course/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| post<br>/course/course/:courseid/user/:userid/status/:status|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| get<br>/course/course/:courseid/user|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| get<br>/course/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| PUT<br>/DB/course/course/:courseid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| POST<br>/DB/coursestatus|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/DB/user/course/:courseid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/DB/course/user/:userid|
|Beschreibung| für den Befehl controller|

||postCourse|
| :----------- |:-----: |
|Ziel| DBCourse|
|Befehl| POST<br>/course|
|Beschreibung| für den Befehl postCourse|

||deleteCourse|
| :----------- |:-----: |
|Ziel| DBCourse|
|Befehl| DELETE<br>/course/:courseid|
|Beschreibung| für den Befehl deleteCourse|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LCourse als lokales Objekt aufgerufen werden kann|


Stand 30.06.2017
