<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since -
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2017
  -
 -->

## Eingänge
---------------

|||
| :----------- |:-----: |
|Befehl| post<br>/link/course/:courseid/extension/:name|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| delete<br>/link/course/:courseid/extension/:name|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| delete<br>/link/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| get<br>/link/exists/course/:courseid/extension/:name|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| get<br>/link/course/:courseid/extension|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| get<br>/link/extension|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| get<br>/link/exists/extension/:name|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| get<br>/link/extension/:name|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||extension|
| :----------- |:-----: |
|Ziel| |
|Befehl| DELETE<br>/course/:courseid|
|Beschreibung| für den Befehl extension|

||extension|
| :----------- |:-----: |
|Ziel| |
|Befehl| POST<br>/course|
|Beschreibung| für den Befehl extension|

||extension|
| :----------- |:-----: |
|Ziel| |
|Befehl| GET<br>/link/exists/course/:courseid|
|Beschreibung| für den Befehl extension|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LExtension als lokales Objekt aufgerufen werden kann|


Stand 30.06.2017
