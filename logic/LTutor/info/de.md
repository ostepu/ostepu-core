## Eingänge
---------------

|||
| :----------- |:-----: |
|Beschreibung| dieser Befehl verteilt die Einsendungen der Studenten gleichmäßig auf die übergebenen Tutoren (Aufgabenweise)|
|Befehl| POST<br>/tutor/auto/exercise/course/:courseid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| dieser Befehl verteilt die Einsendungen der Studenten gleichmäßig auf die übergebenen Tutoren (Gruppenweise)|
|Befehl| POST<br>/tutor/auto/group/course/:courseid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/tutor/user/:userid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| POST<br>/tutor/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| PUT<br>/DB/marking/:marking|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| POST<br>/DB/file|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/DB/file/hash/:hash|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| POST<br>/FS/file|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/DB/user:userid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/FS/path+|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| POST<br>/FS/zip|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/DB/submission/submission/:submissionid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/DB/user/user/:userid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/DB/exercise/exercisesheet/:sheetid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/DB/marking/exercisesheet/:sheetid/tutor/:userid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| POST<br>/DB/marking|
|Beschreibung| für den Befehl controller|

||postTransaction|
| :----------- |:-----: |
|Ziel| DBTransaction|
|Befehl| POST<br>/transaction/exercisesheet/:sheetid|
|Beschreibung| für den Befehl postTransaction|

||out2|
| :----------- |:-----: |
|Ziel| DBQuery2|
|Befehl| POST<br>/query|
|Beschreibung| über diesen Ausgang werden alle übrigen Anfragen behandelt (DEPRECATED)|

||getCourse|
| :----------- |:-----: |
|Ziel| DBCourse|
|Befehl| GET<br>/course/exercisesheet/:esid|
|Beschreibung| für den Befehl getCourse|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LTutor als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|postCourse|
| :----------- |:-----: |
|Ziel| LCourse|
|Beschreibung| wenn eine neue Veranstaltung angelegt wird, dann wollen wir auch aufgerufen werden|


Stand 30.06.2017
