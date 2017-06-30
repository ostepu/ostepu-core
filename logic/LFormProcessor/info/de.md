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

||pdf|
| :----------- |:-----: |
|Ziel| FSPdf|
|Befehl| POST<br>/pdf|
|Beschreibung| für den Befehl pdf|


## Anbindungen
---------------

|Ausgang|postCourse|
| :----------- |:-----: |
|Ziel| LForm|

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LFormProcessor als lokales Objekt aufgerufen werden kann|

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CHelp|
|Beschreibung| hier werden Hilfedateien beim zentralen Hilfesystem angemeldet, sodass sie über ihre globale Adresse abgerufen werden können|


Stand 30.06.2017
