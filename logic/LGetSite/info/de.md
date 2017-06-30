Die LGetSite sammelt und modeliert die Daten für die Benutzerschicht.

## Eingänge
---------------

|||
| :----------- |:-----: |
|Befehl| GET<br>/tutorassign/user/:userid/course/:courseid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/student/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/accountsettings/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/createsheet/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/index/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/coursemanagement/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/mainsettings/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/upload/user/:userid/course/:courseid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/tutorupload/user/:userid/course/:courseid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/markingtool/user/:userid/course/:courseid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/markingtool/user/:userid/course/:courseid/exercisesheet/:sheetid/tutor/:tutorid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/markingtool/user/:userid/course/:courseid/exercisesheet/:sheetid/status/:statusid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/markingtool/user/:userid/course/:courseid/exercisesheet/:sheetid/tutor/:tutorid/status/:statusid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/markingtool/course/:courseid/sheet/:sheetid(/)|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/uploadhistory/user/:userid/course/:courseid/exercisesheet/:sheetid/uploaduser/:uploaduserid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/uploadhistoryoptions/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/tutor/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/admin/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/lecturer/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/group/user/:userid/course/:courseid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/condition/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/condition/user/:userid/course/:courseid/lastsheet/:maxsid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>/condition/user/:userid/course/:courseid/firstsheet/:minsid/lastsheet/:maxsid|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LGetSite als lokales Objekt aufgerufen werden kann|


Stand 30.06.2017
